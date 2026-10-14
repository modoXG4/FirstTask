@extends('layouts.app')

@section('title', __('ui.notifications.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bell me-2"></i>{{ __('ui.notifications.title') }}</h2>
    @if($notifications->count() > 0 && $unreadCount > 0)
    <form action="{{ route('notifications.readAll') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-check-double me-1"></i>{{ __('ui.notifications.mark_all_read') }}
        </button>
    </form>
    @endif
</div>

@if($notifications->isEmpty())
<div class="card" id="empty-notifications">
    <div class="empty-state text-center p-4">
        <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
        <h5>{{ __('ui.notifications.empty_title') }}</h5>
        <p class="text-muted">{{ __('ui.notifications.empty_desc') }}</p>
    </div>
</div>
<div class="card d-none" id="notifications-card">
    <div class="list-group list-group-flush" id="notifications-list"></div>
</div>
@else
<div class="card" id="notifications-card">
    <div class="list-group list-group-flush" id="notifications-list">
        @foreach($notifications as $notification)
        <div class="list-group-item d-flex justify-content-between align-items-start {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    @if(is_null($notification->read_at))
                    <span class="badge bg-primary rounded-pill">{{ __('ui.notifications.new') }}</span>
                    @endif
                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1 fw-semibold">{{ $notification->data['message'] ?? __('ui.notifications.default') }}</p>
                @if(isset($notification->data['task_id']))
                <a href="{{ route('tasks.show', $notification->data['task_id']) }}" class="btn btn-sm btn-outline-primary mt-1">
                    <i class="fas fa-eye me-1"></i>{{ __('ui.notifications.view_task') }}
                </a>
                @endif
            </div>
            @if(is_null($notification->read_at))
            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="ms-3">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('ui.notifications.mark_read') }}">
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
@endsection
