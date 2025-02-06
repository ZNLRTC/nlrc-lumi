<?php

namespace App\Models\Traits;

trait IsActiveTrait
{
    // Prefixing functions with scope allow you to chain query constraints,
    // which is useful for code readability and the DRY principle
    // Sample usage: $active_trainees = Trainee::isActive()->get();
    protected function scopeIsActive($query)
    {
        return $query->where('active', true);
    }

    protected function scopeIsNotActive($query)
    {
        return $query->where('active', false);
    }
}
