<?php

namespace App\Traits;

use App\Models\CustomsOffice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasOffice
{
    /**
     * Scope to filter records by the user's assigned customs office
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeForUserOffice(Builder $query): Builder
    {
        if (!Auth::check() || !Auth::user()->customs_office_id) {
            return $query;
        }

        return $query->where('customs_office_id', Auth::user()->customs_office_id);
    }

    /**
     * Check if the user has access to a specific customs office
     *
     * @param CustomsOffice|int $office
     * @return bool
     */
    public function hasAccessToOffice(CustomsOffice|int $office): bool
    {
        if (!$this->customs_office_id) {
            return false;
        }

        $officeId = $office instanceof CustomsOffice ? $office->id : $office;

        return $this->customs_office_id === $officeId;
    }

    /**
     * Get the assigned customs office
     */
    public function getAssignedOffice(): ?CustomsOffice
    {
        return $this->customsOffice;
    }

    /**
     * Check if the user is an agent (limited to their office)
     */
    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    /**
     * Check if the user is a manager (can see multiple offices)
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    /**
     * Check if the user is an admin (can see everything)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
