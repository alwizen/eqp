<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return true;
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return true;
    }

    public function restore(User $user, Vendor $vendor): bool
    {
        return true;
    }

    public function forceDelete(User $user, Vendor $vendor): bool
    {
        return true;
    }
}
