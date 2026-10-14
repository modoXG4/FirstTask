@extends('layouts.app')

@php use App\Http\Controllers\enums\TaskStatus; @endphp

@section('title', $task->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">{{ __('ui.projects.title') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a></li>
                <li class="breadcrumb-item active">{{ $task->title }}</li>
            </ol>
        </nav>
        <h2 class="mb-0">{{ $task->title }}</h2>
    </div>
    <span class="status-badge status-{{ $task->status->value }} fs-6">
        {{ __('ui.status.' . $task->status->value) }}
    </span>
</div>

<div class="row">
    <div class="col-lg-8">
        @if($task->description)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>{{ __('ui.tasks.description') }}
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $task->description }}</p>
            </div>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-comments me-2"></i>{{ __('ui.comments.title', ['count' => $task->comments->count()]) }}</span>
            </div>
            <div class="card-body">
                @if($task->comments->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comment-dots d-block mb-2" style="font-size: 2rem; opacity: 0.4;"></i>
                    <p class="mb-0">{{ __('ui.comments.empty_title') }}</p>
                    <small>{{ __('ui.comments.empty_desc') }}</small>
                </div>
                @else
                @foreach($task->comments as $comment)
                <div class="comment-item">
                    <div class="d-flex gap-3">
                        <div class="comment-avatar">
                            {{ $comment->user ? mb_substr($comment->user->name, 0, 1) : '?' }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>{{ $comment->user->name ?? '—' }}</strong>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0">{{ $comment->content }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            <div class="card-footer">
                <form action="{{ route('tasks.comments.store', $task) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control @error('content') is-invalid @enderror"
                               name="content" placeholder="{{ __('ui.comments.placeholder') }}" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>{{ __('ui.comments.send') }}
                        </button>
                    </div>
                    @error('content')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info me-2"></i>{{ __('ui.tasks.details') }}
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-0" style="width: 40%;">{{ __('ui.tasks.status') }}</td>
                        <td>
                            <span class="status-badge status-{{ $task->status->value }}">
                                {{ __('ui.status.' . $task->status->value) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">{{ __('ui.tasks.assignee') }}</td>
                        <td>
                            @if($task->assignee)
                                <i class="fas fa-user-circle me-1"></i>{{ $task->assignee->name }}
                            @else
                                <span class="text-muted">{{ __('ui.projects.unassigned') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">{{ __('ui.tasks.due_date') }}</td>
                        <td>
                            @if($task->due_date)
                                <span class="{{ $task->due_date->isPast() && $task->status !== TaskStatus::COMPLETED ? 'text-danger fw-bold' : '' }}">
                                    {{ $task->due_date->format('Y-m-d') }}
                                </span>
                            @else
                                <span class="text-muted">{{ __('ui.common.not_set') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">{{ __('ui.tasks.project') }}</td>
                        <td>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                <i class="fas fa-folder me-1"></i>{{ $task->project->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">{{ __('ui.tasks.created_at') }}</td>
                        <td>{{ $task->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if(auth()->id() === $task->project->owner_id)
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-user-plus me-2"></i>{{ __('ui.tasks.assign_title') }}
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.assign', $task) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <select name="assigned_to" class="form-select">
                            <option value="">{{ __('ui.projects.unassign') }}</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <i class="fas fa-cog me-2"></i>{{ __('ui.common.actions') }}
            </div>
            <div class="card-body d-grid gap-2">
                @if(auth()->id() === $task->project->owner_id)
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-sync me-2"></i>{{ __('ui.tasks.change_status') }}
                </button>
                @else
                <div class="alert alert-info small mb-0 text-center">
                    <i class="fas fa-eye me-1"></i>{{ __('ui.tasks.view_only') }}
                </div>
                @endif
                @if(auth()->id() === $task->project->owner_id)
                <a href="{{ route('projects.show', $task->project) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>{{ __('ui.tasks.back_project') }}
                </a>
                @else
                <a href="{{ route('tasks.assigned') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>{{ __('ui.tasks.back_my_tasks') }}
                </a>
                @endif
                @if(auth()->id() === $task->project->owner_id)
                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                      onsubmit="return confirm('{{ __('ui.confirm.delete_task') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>{{ __('ui.tasks.delete') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('ui.tasks.change_status_title', ['title' => $task->title]) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="status" class="form-select">
                        @foreach(\App\Http\Controllers\enums\TaskStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ $task->status === $status ? 'selected' : '' }}>{{ __('ui.status.' . $status->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ __('ui.common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
