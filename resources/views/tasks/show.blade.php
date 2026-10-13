@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a></li>
                <li class="breadcrumb-item active">{{ $task->title }}</li>
            </ol>
        </nav>
        <h2 class="mb-0">{{ $task->title }}</h2>
    </div>
    <span class="status-badge status-{{ $task->status->value }} fs-6">
        @if($task->status === TaskStatus::PENDING) معلق
        @elseif($task->status === TaskStatus::IN_PROGRESS) قيد التنفيذ
        @elseif($task->status === TaskStatus::COMPLETED) مكتمل
        @elseif($task->status === TaskStatus::CANCELLED) ملغي
        @endif
    </span>
</div>

<div class="row">
    <div class="col-lg-8">
        @if($task->description)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>الوصف
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $task->description }}</p>
            </div>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-comments me-2"></i>التعليقات ({{ $task->comments->count() }})</span>
            </div>
            <div class="card-body">
                @if($task->comments->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comment-dots d-block mb-2" style="font-size: 2rem; opacity: 0.4;"></i>
                    <p class="mb-0">لا توجد تعليقات بعد</p>
                    <small>كن أول من يعلق</small>
                </div>
                @else
                @foreach($task->comments as $comment)
                <div class="comment-item">
                    <div class="d-flex gap-3">
                        <div class="comment-avatar">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>{{ $comment->user->name }}</strong>
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
                               name="content" placeholder="اكتب تعليقاً..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>إرسال
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
                <i class="fas fa-info me-2"></i>تفاصيل المهمة
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-0" style="width: 40%;">الحالة</td>
                        <td>
                            <span class="status-badge status-{{ $task->status->value }}">
                                @if($task->status === TaskStatus::PENDING) معلق
                                @elseif($task->status === TaskStatus::IN_PROGRESS) قيد التنفيذ
                                @elseif($task->status === TaskStatus::COMPLETED) مكتمل
                                @elseif($task->status === TaskStatus::CANCELLED) ملغي
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">المسؤول</td>
                        <td>
                            @if($task->assignee)
                                <i class="fas fa-user-circle me-1"></i>{{ $task->assignee->name }}
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">تاريخ التسليم</td>
                        <td>
                            @if($task->due_date)
                                <span class="{{ $task->due_date->isPast() && $task->status !== TaskStatus::COMPLETED ? 'text-danger fw-bold' : '' }}">
                                    {{ $task->due_date->format('Y-m-d') }}
                                </span>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">المشروع</td>
                        <td>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                <i class="fas fa-folder me-1"></i>{{ $task->project->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">أنشئ في</td>
                        <td>{{ $task->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if(auth()->id() === $task->project->owner_id)
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-user-plus me-2"></i>إسناد المهمة
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.assign', $task) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <select name="assigned_to" class="form-select">
                            <option value="">-- إلغاء الإسناد --</option>
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
                <i class="fas fa-cog me-2"></i>الإجراءات
            </div>
            <div class="card-body d-grid gap-2">
                @if(auth()->id() === $task->project->owner_id)
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-sync me-2"></i>تغيير الحالة
                </button>
                @else
                <div class="alert alert-info small mb-0 text-center">
                    <i class="fas fa-eye me-1"></i>وضع المشاهدة فقط
                </div>
                @endif
                @if(auth()->id() === $task->project->owner_id)
                <a href="{{ route('projects.show', $task->project) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>العودة للمشروع
                </a>
                @else
                <a href="{{ route('tasks.assigned') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>العودة لمهامي
                </a>
                @endif
                @if(auth()->id() === $task->project->owner_id)
                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>حذف المهمة
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
