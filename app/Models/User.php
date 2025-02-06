<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Observer;
use App\Models\Role;
use App\Models\Trainee;
use App\Models\Grading\Grade;
use App\Models\Meetings\Meeting;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Grouping\GroupTrainee;
use App\Models\Traits\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'profile_photo_path',
        'website_photo_path',
        'timezone',
        'notification_settings',
        'password',
        'restricted',
        'notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
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
            'notification_settings' => 'array'
        ];
    }

    // Staff avatars on the admin panel
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->website_photo_path)
        {
            return Storage::disk('avatars_website')->url($this->website_photo_path);
        }

        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Email verification should be enabled in production
        // return $this->hasVerifiedEmail() && (str_ends_with($this->email, '@nlrc.ph') || str_ends_with($this->email, '@nlrc.fi')) && $this->hasAnyRole(['Admin','Manager','Staff','Editing instructor']);
        return (str_ends_with($this->email, '@nlrc.ph') || str_ends_with($this->email, '@nlrc.fi') || str_starts_with($this->email, 'observer@') || $this->email == 'storm_iz_here@yahoo.com') &&
            $this->hasAnyRole(['Admin', 'Staff', 'Observer', 'Editing instructor', 'Manager']);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->role?->name === $roleName;
    }

    /**
     * Check if the user has any of the specified roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        // First check if the role relationship is loaded already. Otherwise, the policy check is done separately for each row in the table views, making Filament send a million queries
        if ($this->relationLoaded('role')) {
            return in_array($this->role?->name, $roles);
        }

        return $this->role()->whereIn('name', $roles)->exists();
    }

    public function trainee(): HasOne
    {
        return $this->hasOne(Trainee::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function groupTraineeAdds(): HasMany
    {
        return $this->hasMany(GroupTrainee::class, 'added_by');
    }

    public function meetingAdds(): HasMany
    {
        return $this->hasMany(Meeting::class, 'instructor');
    }

    public function gradeAdds(): HasMany
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }

    public function observer(): HasOne
    {
        return $this->hasOne(Observer::class);
    }
}
