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

class TaskController extends Controller
{
    use ApiResponseTrait;

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

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
        ]);

        $project->status = 'in_progress';
        $project->save();

        $this->sendNotificationToAssignee($task);

        if ($request->expectsJson()) {
            return $this->success(new TaskResource($task->load('assignee')), 'Task created successfully', 201);
        }

        return redirect()->route('projects.show', $project)->with('success', 'تم إضافة المهمة بنجاح.');
    }

    public function show(Request $request, Task $task)
    {
        $task->load(['project', 'assignee', 'comments' => function ($query) {
            $query->with('user')->latest();
        }]);

        // Allow view if owner or assigned to user
        if ($task->project->user_id !== $request->user()->id && $task->assigned_to !== $request->user()->id) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        if ($request->expectsJson()) {
            return $this->success(new TaskResource($task));
        }

        $users = \App\Models\User::all();
        return view('tasks.show', compact('task', 'users'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed,cancelled'],
        ]);

        if (Gate::denies('update', $task)) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        if ($task->status === 'completed') {
            if ($request->expectsJson()) {
                return $this->error('Cannot change status of a completed task', 422);
            }
            return back()->with('error', 'لا يمكن تغيير حالة مهمة مكتملة.');
        }

        if ($request->status === 'completed' && $task->status !== 'in_progress') {
            if ($request->expectsJson()) {
                return $this->error('Task must be in_progress before marking as completed', 422);
            }
            return back()->with('error', 'يجب أن تكون المهمة قيد التنفيذ أولاً.');
        }

        $task->update(['status' => $request->status]);

        $this->sendNotificationToAssignee($task);

        $project = $task->project;
        $project->status = 'completed';
        foreach ($project->tasks as $projectTask) {
            if ($projectTask->status !== 'completed') {
                $project->status = 'in_progress';
                break;
            }
        }
        $project->save();

        if ($request->expectsJson()) {
            return $this->success(new TaskResource($task->load('assignee')), 'Task status updated successfully');
        }

        return back()->with('success', 'تم تحديث الحالة بنجاح.');
    }

    public function destroy(Request $request, Task $task)
    {
        if (Gate::denies('delete', $task)) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        $project = $task->project;
        $task->delete();

        if ($request->expectsJson()) {
            return $this->success(null, 'Task deleted successfully');
        }

        return redirect()->route('projects.show', $project)->with('success', 'تم حذف المهمة بنجاح.');
    }

    public function assign(StoreAssinedTo $request, Task $task)
    {
        if (Gate::denies('update', $task)) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        $task->update(['assigned_to' => $request->assigned_to]);

        $this->sendNotificationToAssignee($task);

        if ($request->expectsJson()) {
            return $this->success(new TaskResource($task->load('assignee')), 'Task assigned successfully');
        }

        return back()->with('success', 'تم إسناد المهمة بنجاح.');
    }

    public function assigned(Request $request)
    {
        $tasks = $request->user()->assignedTasks()->with(['project', 'assignee'])->latest()->get();

        if ($request->expectsJson()) {
            return $this->success(TaskResource::collection($tasks));
        }

        return view('tasks.assigned', compact('tasks'));
    }

    public function setDueDate(StoreDueDate $request, Task $task)
    {
        $task->update(['due_date' => $request->due_date]);

        return $this->success(new TaskResource($task->load('assignee')), 'Task due date updated successfully');
    }

    private function sendNotificationToAssignee(Task $task)
    {
        $assignee = $task->assignee;
        $project_owner = $task->project->user;
        if ($task->status !== 'completed' && $assignee && $assignee->id !== $project_owner->id) {
            $assignee->notify(new TaskAssignedNotification($task));
            NotificationSent::dispatch($assignee->id, 'تم إسناد مهمة جديدة لك: ' . $task->title,$task->id);
        }
        if ($task->status === 'completed' && $project_owner && $project_owner->id !== auth()->user()->id) {
            $project_owner->notify(new TaskCompletedNotification($task));
            NotificationSent::dispatch($project_owner->id, 'تم إكمال المهمة: ' . $task->title,$task->id);
        }
    }
}
