@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bell me-2"></i>الإشعارات</h2>
    @if($notifications->count() > 0 && $unreadCount > 0)
    <form action="{{ route('notifications.readAll') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-check-double me-1"></i>تحديد الكل كمقروء
        </button>
    </form>
    @endif
</div>

@if($notifications->isEmpty())
<div class="card" id="empty-notifications">
    <div class="empty-state text-center p-4">
        <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
        <h5>لا توجد إشعارات</h5>
        <p class="text-muted">ستظهر هنا الإشعارات عند إسناد مهام أو إكمالها</p>
    </div>
</div>
<div class="card d-none" id="notifications-card">
    <div class="list-group list-group-flush" id="notifications-list"></div>
</div>
@else
<div class="card">
    <div class="list-group list-group-flush" id="notifications-list">
        @foreach($notifications as $notification)
        <div class="list-group-item d-flex justify-content-between align-items-start {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    @if(is_null($notification->read_at))
                    <span class="badge bg-primary rounded-pill">جديد</span>
                    @endif
                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1 fw-semibold">{{ $notification->data['message'] ?? 'إشعار' }}</p>
                @if(isset($notification->data['task_id']))
                <a href="{{ route('tasks.show', $notification->data['task_id']) }}" class="btn btn-sm btn-outline-primary mt-1">
                    <i class="fas fa-eye me-1"></i>عرض المهمة
                </a>
                @endif
            </div>
            @if(is_null($notification->read_at))
            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="ms-3">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-outline-success" title="تحديد كمقروء">
                    <i class="fas fa-check"></i>
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
</div>
<div class="mt-3">
    {{ $notifications->links() }}
</div>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    const userId = {{ auth()->id() }};

    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            console.log('إشعار جديد وصل:', notification);

            // إخفاء الـ Empty State
            const emptyState = document.getElementById('empty-notifications');
            const notificationsCard = document.getElementById('notifications-card');
            if (emptyState) emptyState.classList.add('d-none');
            if (notificationsCard) notificationsCard.classList.remove('d-none');

            const list = document.getElementById('notifications-list');
            if (!list) return;

            // إضافة الإشعار الجديد أعلى القائمة
            const newItem = document.createElement('div');
            newItem.className = 'list-group-item d-flex justify-content-between align-items-start bg-light';

            let taskBtnHtml = '';
            if (notification.task_id) {
                taskBtnHtml = `
                    <a href="/tasks/${notification.task_id}" class="btn btn-sm btn-outline-primary mt-1">
                        <i class="fas fa-eye me-1"></i>عرض المهمة
                    </a>
                `;
            }

            newItem.innerHTML = `
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary rounded-pill">جديد</span>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>الآن</small>
                    </div>
                    <p class="mb-1 fw-semibold">${notification.message}</p>
                    ${taskBtnHtml}
                </div>
            `;

            list.prepend(newItem);
        });
});
</script>
@endsection