<?php

 namespace App\Policies;
 
class commentPolicy
{
    public function viewAny($user)
    {
        return false;
    }
    public function view($user, $comment)
    {
        return $user->id === $comment->task->assignee->id || $user->id === $comment->task->project->owner_id;
    }
    public function create($user, $task)
    {
        return $user->id === $task->assignee->id || $user->id === $task->project->owner_id;
    }
    public function update($user, $comment)
    {
        return false; // No one can update comments
    }
    public function delete($user, $comment)
    {
        return false; // No one can delete comments
    }
}