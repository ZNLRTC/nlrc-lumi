<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    public function profilePhotoUrl()
    {
        return $this->getPhotoUrl('avatars', $this->profile_photo_path);
    }

    public function websitePhotoUrl()
    {
        return $this->getPhotoUrl('avatars_website', $this->website_photo_path);
    }

    private function getPhotoUrl($disk, $path)
    {
        if ($path) {
            return Storage::disk($disk)->url($path);
        }

        $initials = $this->getInitials();

        // Default photo URL with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&color=085389&background=a7c7e6';
    }

    // Prevents usernames and real names from leaking to ui-avatars
    private function getInitials()
    {
        $name = Auth::user()->hasRole('Trainee') && $this->trainee
            ? $this->trainee->first_name . ' ' . $this->trainee->last_name
            : $this->name;

        return Str::of($name)
            ->split('/\s+/')
            ->map(fn ($parfOfName) => Str::substr($parfOfName, 0 , 1))
            ->join('');
    }
}
