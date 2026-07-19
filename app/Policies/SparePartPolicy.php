<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SparePart;
use App\Models\User;

class SparePartPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SparePart $sparePart): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SparePart $sparePart): bool
    {
        return true;
    }

    public function delete(User $user, SparePart $sparePart): bool
    {
        return true;
    }

    public function restore(User $user, SparePart $sparePart): bool
    {
        return true;
    }

    public function forceDelete(User $user, SparePart $sparePart): bool
    {
        return true;
    }
}
