<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, HasLocalePreference, MustVerifyEmail
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
            'notify_budget_warnings' => 'boolean',
            'notify_budget_limit_email' => 'boolean',
        ];
    }

    public function preferredLocale(): string
    {
        return $this->locale ?: app()->getLocale();
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
}
