@extends('layouts.app')

@section('title', __('ui.projects.title'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-folder me-2"></i>{{ __('ui.projects.title') }}</h2>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('ui.projects.new') }}
    </a>
</div>

@if($projects->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="fas fa-folder-open"></i>
        <h5>{{ __('ui.projects.empty_title') }}</h5>
        <p>{{ __('ui.projects.empty_desc') }}</p>
        <a href="{{ route('projects.create') }}" class="btn btn-primary mt-2">
            <i class="fas fa-plus me-2"></i>{{ __('ui.projects.create') }}
        </a>
    </div>
</div>
@else
<div class="row">
    @foreach($projects as $project)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0">{{ $project->name }}</h5>
                    <span class="status-badge status-{{ $project->status }}">
                        {{ __('ui.status.' . $project->status) }}
                    </span>
                </div>
                <p class="card-text text-muted small">
                    {{ $project->description ? Str::limit($project->description, 80) : __('ui.projects.no_description') }}
                </p>
                <div class="d-flex align-items-center text-muted small">
                    <span class="me-3">
                        <i class="fas fa-tasks me-1"></i>{{ __('ui.projects.tasks_count', ['count' => $project->tasks_count]) }}
                    </span>
                    <span>
                        <i class="fas fa-calendar me-1"></i>{{ $project->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="d-flex gap-2">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-eye me-1"></i>{{ __('ui.common.view') }}
                    </a>
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-edit me-1"></i>{{ __('ui.common.edit') }}
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                          onsubmit="return confirm('{{ __('ui.confirm.delete_project') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('ui.common.delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
