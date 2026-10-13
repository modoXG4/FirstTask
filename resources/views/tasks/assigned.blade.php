@extends('layouts.app')

@section('title', 'مهامي')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-check me-2"></i>المهام الموكلة إلي</h2>
    <p class="text-muted">كل المهام التي تم إسنادها إليك من جميع المشاريع</p>
</div>

@if($tasks->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="fas fa-clipboard-check"></i>
        <h5>لا توجد مهام موكلة إليك</h5>
        <p>عندما يسند إليك أحد مهمة ستظهر هنا</p>
    </div>
</div>
@else
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>المهمة</th>
                    <th>المشروع</th>
                    <th>الحالة</th>
                    <th>تاريخ التسليم</th>
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
                            @if($task->status === TaskStatus::PENDING) معلق
                            @elseif($task->status === TaskStatus::IN_PROGRESS) قيد التنفيذ
                            @elseif($task->status === TaskStatus::COMPLETED) مكتمل
                            @elseif($task->status === TaskStatus::CANCELLED) ملغي
                            @endif
                        </span>
                    </td>
                    <td>
                        @if($task->due_date)
                            <span class="{{ $task->due_date->isPast() && $task->status !== TaskStatus::COMPLETED ? 'text-danger fw-bold' : '' }}">
                                {{ $task->due_date->format('Y-m-d') }}
                            </span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
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
