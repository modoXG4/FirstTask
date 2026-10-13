<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssinedTo;
use App\Http\Requests\StoreDueDate;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\NotificationSent;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;
use App\http\Controllers\Enums\TaskStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\ExpectsJsonTrait;

class TaskController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests , ExpectsJsonTrait;

    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }
    public function index(Request $request, $projectId)
    {
        $project = $request->user()->projects()->findOrFail($projectId);

        $tasks = Task::where('project_id', $projectId)
            ->with('assignee')
            ->get();

        return $this->success(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request)
    {
        $project = $request->user()->projects()->findOrFail($request->project_id);

        $task = Task::create($request->validated());

        $project->status = 'in_progress';
        $project->save();

        $this->sendNotificationToAssignee($task);

       return $this->respondBasedOnRequest($request, new TaskResource($task->load('assignee')), 'projects.show', __('tasks.created'));
    }

    public function show(Request $request, Task $task)
    {
        $task->load(['project', 'assignee', 'comments' => function ($query) {
            $query->with('user')->latest();
        }]);

        // Allow view if owner or assigned to user
        if ($task->project->owner_id !== $request->user()->id && $task->assigned_to !== $request->user()->id) {
           return $this->respondBasedOnRequest($request, null, null, 'Unauthorized', 403);
        }

        if ($request->expectsJson()) {
            return $this->success(new TaskResource($task));
        }

        $users = \App\Models\User::all();
        return view('tasks.show', compact('task', 'users'));
    }

    public function updateStatus(UpdateStatusRequest $request, Task $task)
    {

        if ($task->status === TaskStatus::COMPLETED) {
            return $this->respondBasedOnRequest($request, null, null, 'Task is already completed', 422);   
        }

        if ($request->validated()['status'] === TaskStatus::COMPLETED->value && $task->status !== TaskStatus::IN_PROGRESS) {
            return $this->respondBasedOnRequest($request, null, null, 'Task must be in_progress before marking as completed', 422);
        }

        $task->update($request->validated());

        $this->sendNotificationToAssignee($task);


        return $this->respondBasedOnRequest($request, new TaskResource($task->load('assignee')), 'projects.show', __('tasks.status_updated'));
    }

    public function destroy(Request $request, Task $task)
    {

        $project = $task->project;
        $task->delete();

        return $this->respondBasedOnRequest($request, null, 'projects.show', __('tasks.deleted'));
    }

    public function assign(StoreAssinedTo $request, Task $task)
    {
        $this->authorize('assign', $task);
        $task->update($request->validated());

        $this->sendNotificationToAssignee($task);

        return $this->respondBasedOnRequest($request, new TaskResource($task->load('assignee')), 'projects.show', __('tasks.assigned'));
    }

    public function assigned(Request $request)
    {
        $tasks = $request->user()->assignedTasks()->with(['project', 'assignee'])->latest()->get();

        return $this->success(TaskResource::collection($tasks));
    }

    public function setDueDate(StoreDueDate $request, Task $task)
    {
        $this->authorize('setDate', $task);
        $task->update($request->validated());

        return $this->respondBasedOnRequest($request, new TaskResource($task->load('assignee')), 'projects.show', __('tasks.due_date_updated'));
    }

    private function sendNotificationToAssignee(Task $task)
    {
        $assignee = $task->assignee;
        $project_owner = $task->project->user;
        if ($task->status !== TaskStatus::COMPLETED && $assignee && $assignee->id !== $project_owner->id) {
            $assignee->notify(new TaskAssignedNotification($task));
        }
        if ($task->status === TaskStatus::COMPLETED && $project_owner && $project_owner->id !== auth()->user()->id) {
            $project_owner->notify(new TaskCompletedNotification($task));
        }
    }
}
