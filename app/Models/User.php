<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url', // Add avatar_url to mass assignable attributes
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
        ];
    }

    /**
     * Get the URL of the user's avatar for Filament.
     *
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Check if avatar_url is set
        if ($this->avatar_url) {
            // Generate the correct URL to the avatar file
            return asset('storage/' . $this->avatar_url);
        }

        // Return a default avatar URL if none is set
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
}
