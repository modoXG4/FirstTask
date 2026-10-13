@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>
                <li class="breadcrumb-item active">{{ $project->name }}</li>
            </ol>
        </nav>
        <h2 class="mb-0">{{ $project->name }}</h2>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="status-badge status-{{ $project->status }} fs-6">
            @if($project->status === 'pending') معلق
            @elseif($project->status === 'in_progress') قيد التنفيذ
            @elseif($project->status === 'completed') مكتمل
            @elseif($project->status === 'cancelled') ملغي
            @endif
        </span>
    </div>
</div>

@if($project->description)
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-0">{{ $project->description }}</p>
    </div>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>المهام ({{ $project->tasks->count() }})</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        <i class="fas fa-plus me-2"></i>إضافة مهمة
    </button>
</div>

@if($project->tasks->isEmpty())
<div class="card mb-4">
    <div class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <h5>لا توجد مهام بعد</h5>
        <p>أضف مهمة جديدة لبدء العمل</p>
        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="fas fa-plus me-2"></i>إضافة مهمة
        </button>
    </div>
</div>
@else
<div class="card mb-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>المهمة</th>
                    <th>الحالة</th>
                    <th>المسؤول</th>
                    <th>تاريخ التسليم</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->tasks as $task)
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
                        <span class="status-badge status-{{ $task->status->value }}">
                            @if($task->status === TaskStatus::PENDING) معلق
                            @elseif($task->status === TaskStatus::IN_PROGRESS) قيد التنفيذ
                            @elseif($task->status === TaskStatus::COMPLETED) مكتمل
                            @elseif($task->status === TaskStatus::CANCELLED) ملغي
                            @endif
                        </span>
                    </td>
                    <td>
                        @if($task->assignee)
                            <span><i class="fas fa-user-circle me-1 text-muted"></i>{{ $task->assignee->name }}</span>
                        @else
                            <span class="text-muted"><i class="fas fa-user-slash me-1"></i>غير محدد</span>
                        @endif
                    </td>
                    <td>
                        @if($task->due_date)
                            <span class="{{ $task->due_date->isPast() && $task->status !== TaskStatus::COMPLETED ? 'text-danger fw-bold' : '' }}">
                                <i class="fas fa-calendar me-1"></i>{{ $task->due_date->format('Y-m-d') }}
                            </span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                    data-bs-target="#assignModal{{ $task->id }}" title="إسناد">
                                <i class="fas fa-user-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                    data-bs-target="#statusModal{{ $task->id }}" title="تغيير الحالة">
                                <i class="fas fa-sync"></i>
                            </button>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                   onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        <div class="modal fade" id="assignModal{{ $task->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tasks.assign', $task) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">إسناد: {{ $task->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <select name="assigned_to" class="form-select">
                                                <option value="">-- إلغاء الإسناد --</option>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary">حفظ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="statusModal{{ $task->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">تغيير حالة: {{ $task->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <select name="status" class="form-select">
                                                <option value="pending" {{ $task->status === TaskStatus::PENDING ? 'selected' : '' }}>معلق</option>
                                                <option value="in_progress" {{ $task->status === TaskStatus::IN_PROGRESS ? 'selected' : '' }}>قيد التنفيذ</option>
                                                <option value="completed" {{ $task->status === TaskStatus::COMPLETED ? 'selected' : '' }}>مكتمل</option>
                                                <option value="cancelled" {{ $task->status === TaskStatus::CANCELLED ? 'selected' : '' }}>ملغي</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary">حفظ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>إضافة مهمة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">عنوان المهمة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               name="title" value="{{ old('title') }}" required
                               placeholder="أدخل عنوان المهمة">
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  name="description" rows="3"
                                  placeholder="وصف مختصر للمهمة (اختياري)">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">المسؤول</label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" name="assigned_to">
                                <option value="">اختر مستخدم</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">تاريخ التسليم</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                   name="due_date" value="{{ old('due_date') }}">
                            @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
