<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['ppic', 'operator', 'qc', 'manager', 'super_admin']);
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['ppic', 'operator', 'qc', 'manager', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ppic', 'super_admin']);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['ppic', 'super_admin']);
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['super_admin']);
    }
}
