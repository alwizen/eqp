<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EquipmentMaintenanceHistory;
use App\Models\User;

class EquipmentMaintenanceHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, EquipmentMaintenanceHistory $history): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, EquipmentMaintenanceHistory $history): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, EquipmentMaintenanceHistory $history): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, EquipmentMaintenanceHistory $history): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, EquipmentMaintenanceHistory $history): bool
    {
        return $this->canManage($user);
    }

    protected function canManage(User $user): bool
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole(['admin', 'manager', 'super-admin']) || $user->hasRole('maintenance-admin');
        }

        return true;
    }
}
