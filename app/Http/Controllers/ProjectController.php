<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $projects = $request->user()->projects()->withCount('tasks')->get();

        if ($request->expectsJson()) {
            return $this->success(ProjectResource::collection($projects));
        }

        return view('projects.index', compact('projects'));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = $request->user()->projects()->create($request->validated());

        if ($request->expectsJson()) {
            return $this->success(new ProjectResource($project->loadCount('tasks')), 'Project created successfully', 201);
        }

        return redirect()->route('projects.index')->with('success', 'تم إنشاء المشروع بنجاح.');
    }

    public function show(Request $request, Project $project)
    {
        if (Gate::denies('view', $project)) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        $project->loadCount('tasks');
        $project->load(['tasks' => function ($query) {
            $query->with('assignee')->latest();
        }]);

        if ($request->expectsJson()) {
            return $this->success(new ProjectResource($project));
        }

        $users = User::all();
        return view('projects.show', compact('project', 'users'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        if (Gate::denies('update', $project)) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        $project->update($request->validated());

        if ($request->expectsJson()) {
            return $this->success(new ProjectResource($project->loadCount('tasks')), 'Project updated successfully');
        }

        return redirect()->route('projects.show', $project)->with('success', 'تم تحديث المشروع بنجاح.');
    }

    public function destroy(Request $request, Project $project)
    {
        if (Gate::denies('delete', $project)) {
            if ($request->expectsJson()) {
                return $this->error('Unauthorized', 403);
            }
            abort(403);
        }

        $project->delete();

        if ($request->expectsJson()) {
            return $this->success(null, 'Project deleted successfully');
        }

        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح.');
    }

    public function create()
    {
        return view('projects.create');
    }

    public function edit(Project $project)
    {
        if (Gate::denies('update', $project)) {
            abort(403);
        }

        return view('projects.edit', compact('project'));
    }
}
