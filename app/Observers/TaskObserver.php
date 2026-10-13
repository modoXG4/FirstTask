<?php

namespace App\Observers;

use App\Http\Controllers\enums\TaskStatus;

class TaskObserver
{
    public function updated($task)
    {
        if(!$task->wasChanged('status')) {
           return; 
        }
        $project = $task->project;
        $this->syncProjectStatus($project);
    }

    public function created($task)
    {
        $project = $task->project;
        $this->syncProjectStatus($project);
    }

    private function syncProjectStatus($project)
    { {
            $project->status = TaskStatus::PENDING;
            $Completed = true;
            foreach ($project->tasks as $projectTask) {
                if ($projectTask->status !== TaskStatus::COMPLETED) {
                    $Completed = false;
                }
                if ($projectTask->status === TaskStatus::IN_PROGRESS) {
                    $project->status = TaskStatus::IN_PROGRESS;
                    break;
                }
                if ($projectTask->status === TaskStatus::PENDING) {
                    $project->status = TaskStatus::PENDING;
                }
            }
            if ($Completed && $project->tasks->count() > 0) {
                $project->status = TaskStatus::COMPLETED;
            }
            $project->save();
        }
    }
}
