@extends('layouts.app')

@php use App\Http\Controllers\enums\TaskStatus; @endphp

@section('title', __('ui.tasks.my_tasks_title'))

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-check me-2"></i>{{ __('ui.tasks.my_tasks_title') }}</h2>
    <p class="text-muted">{{ __('ui.tasks.my_tasks_sub') }}</p>
</div>

@if($tasks->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="fas fa-clipboard-check"></i>
        <h5>{{ __('ui.tasks.empty_title') }}</h5>
        <p>{{ __('ui.tasks.empty_desc') }}</p>
    </div>
</div>
@else
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('ui.projects.task') }}</th>
                    <th>{{ __('ui.tasks.project') }}</th>
                    <th>{{ __('ui.tasks.status') }}</th>
                    <th>{{ __('ui.tasks.due_date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-bold text-primary">
                            {{ $task->title }}
                        </a>
                        @if($task->description)
                        <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                            <i class="fas fa-folder me-1"></i>{{ $task->project->name }}
                        </a>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $task->status->value }}">
                            {{ __('ui.status.' . $task->status->value) }}
                        </span>
                    </td>
                    <td>
                        @if($task->due_date)
                            <span class="{{ $task->due_date->isPast() && $task->status !== TaskStatus::COMPLETED ? 'text-danger fw-bold' : '' }}">
                                {{ $task->due_date->format('Y-m-d') }}
                            </span>
                        @else
                            <span class="text-muted">{{ __('ui.common.not_set') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary" title="{{ __('ui.common.view') }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
