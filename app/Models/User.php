<?php

namespace App\Models;

use App\Exceptions\AiAccessDeniedException;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasLocalePreference, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasPanelShield, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'locale',
        'password',
        'avatar_url', // Add avatar_url to mass assignable attributes
        // Stripe pricing (app-level subscription demo)
        'plan',
        'premium_granted_by_admin',
        'ai_access',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_status',
        'stripe_current_period_end',
        'stripe_cancel_at_period_end',
        'notify_budget_warnings',
        'notify_budget_limit_email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'stripe_current_period_end' => 'date',
            'stripe_cancel_at_period_end' => 'boolean',
            'premium_granted_by_admin' => 'boolean',
            'ai_access' => 'boolean',
            'notify_budget_warnings' => 'boolean',
            'notify_budget_limit_email' => 'boolean',
        ];
    }

    public function preferredLocale(): string
    {
        return $this->locale ?: app()->getLocale();
    }

    public function aiUsageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function hasAiAccess(): bool
    {
        return (bool) $this->ai_access;
    }

    /**
     * AI is opt-in even for Premium (rolling deploy control).
     *
     * @throws AiAccessDeniedException
     */
    public function ensureHasAiAccess(?string $denialMessage = null): void
    {
        if ($this->hasAiAccess()) {
            return;
        }

        throw new AiAccessDeniedException($denialMessage ?? __('messages.ai_access.denied_default', [
            'email' => (string) config('planance.contact_ai_email'),
        ]));
    }

    /**
     * Get the URL of the user's avatar for Filament.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Check if avatar_url is set
        if ($this->avatar_url) {
            // Generate the correct URL to the avatar file
            return asset('storage/'.$this->avatar_url);
        }

        // Return a default avatar URL if none is set
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    /**
     * Active Premium (Stripe subscription or admin-granted complimentary access).
     */
    public function hasPremiumSubscription(): bool
    {
        if (strtolower((string) $this->plan) !== 'premium') {
            return false;
        }

        if ($this->premium_granted_by_admin) {
            return true;
        }

        if (! is_string($this->stripe_subscription_id) || $this->stripe_subscription_id === '') {
            return false;
        }

        $status = strtolower((string) ($this->stripe_status ?? ''));

        return in_array($status, ['active', 'trialing'], true);
    }

    /** Stripe-billed Premium (excludes complimentary admin access). */
    public function hasStripeManagedPremiumSubscription(): bool
    {
        if ($this->premium_granted_by_admin) {
            return false;
        }

        if (strtolower((string) $this->plan) !== 'premium') {
            return false;
        }

        if (! is_string($this->stripe_subscription_id) || $this->stripe_subscription_id === '') {
            return false;
        }

        $status = strtolower((string) ($this->stripe_status ?? ''));

        return in_array($status, ['active', 'trialing'], true);
    }

    /**
     * Filament pricing page path for Stripe return URLs and in-app redirects.
     */
    public function filamentPricingPath(): string
    {
        return $this->hasPremiumSubscription() ? '/premium/pricing' : '/app/pricing';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole(Utils::getSuperAdminName());
        }

        return $this->hasRole(Utils::getSuperAdminName())
            || $this->hasRole(Utils::getPanelUserRoleName());
    }
}
