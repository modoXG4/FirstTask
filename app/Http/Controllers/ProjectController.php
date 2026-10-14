<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Traits\ExpectsJsonTrait;

class ProjectController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests, ExpectsJsonTrait;

    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    public function index(Request $request)
    {
        $projects = $request->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            },
        ])->get();

        if ($request->expectsJson()) {
            return $this->success(['projects' => $projects], __('projects.list_success'));
        }

        return view('projects.index', compact('projects'));
    }

    public function store(StoreProjectRequest $request)
    {
        Cache::forget("user_{$request->user()->id}_projects");
        $project = $request->user()->projects()->create($request->validated());

        return $this->respondBasedOnRequest($request, new ProjectResource($project), 'projects.index', __('projects.created'));
    }

    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $project->loadCount('tasks');
        $project->load(['tasks' => function ($query) {
            $query->with('assignee')->latest();
        }]);

        if ($request->expectsJson()) {
            return $this->success(new ProjectResource($project), __('projects.show_success'));
        }

        $users = User::all();
        return view('projects.show', compact('project', 'users'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $project->update($request->validated());

        return $this->respondBasedOnRequest($request, new ProjectResource($project), 'projects.show', __('projects.updated'), 200, [$project]);
    }

    public function destroy(Request $request, Project $project)
    {
        Cache::forget("user_{$request->user()->id}_projects");
        $this->authorize('delete', $project);
        $project->delete();

        return $this->respondBasedOnRequest($request, null, 'projects.index', __('projects.deleted'));
        
    }

    public function create()
    {
        return view('projects.create');
    }

    public function edit(Project $project)
    {

        return view('projects.edit', compact('project'));
    }
}
