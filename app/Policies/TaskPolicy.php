<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id;
    }
    public function assign(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id;
    }
    public function setDate(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id;
    }
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id;
    }
    public function create(User $user): bool
    {
        return true; // Allow all authenticated users to create tasks
    }
    public function viewAny(User $user): bool
    {
        return true; // Allow all authenticated users to view tasks
    }
    public function updateStatus(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_to;
    }
}
