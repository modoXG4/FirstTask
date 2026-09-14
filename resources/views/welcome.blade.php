@extends('layouts.app')

@section('title', 'مرحباً بك')

@section('content')
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas fa-tasks text-primary" style="font-size: 4rem;"></i>
    </div>
    <h1 class="display-5 fw-bold mb-3">نظام إدارة المهام</h1>
    <p class="lead text-muted mb-4">نظّم مشاريعك ومهامك بسهولة وفعالية</p>

    @auth
    <a href="{{ route('projects.index') }}" class="btn btn-primary btn-lg px-5">
        <i class="fas fa-folder-open me-2"></i>اذهب للمشاريع
    </a>
    @else
    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
            <i class="fas fa-sign-in-alt me-2"></i>تسجيل دخول
        </a>
        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">
            <i class="fas fa-user-plus me-2"></i>حساب جديد
        </a>
    </div>
    @endauth
</div>

<div class="row mt-4 justify-content-center">
    <div class="col-md-4 mb-4">
        <div class="card text-center p-4 h-100">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width: 64px; height: 64px;">
                    <i class="fas fa-folder text-primary" style="font-size: 1.5rem;"></i>
                </div>
            </div>
            <h5 class="fw-bold">المشاريع</h5>
            <p class="text-muted mb-0">أنشئ وأدر مشاريعك بسهولة</p>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-center p-4 h-100">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width: 64px; height: 64px;">
                    <i class="fas fa-clipboard-list text-success" style="font-size: 1.5rem;"></i>
                </div>
            </div>
            <h5 class="fw-bold">المهام</h5>
            <p class="text-muted mb-0">تتبع مهامك وحالاتها</p>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-center p-4 h-100">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10" style="width: 64px; height: 64px;">
                    <i class="fas fa-comments text-info" style="font-size: 1.5rem;"></i>
                </div>
            </div>
            <h5 class="fw-bold">التعليقات</h5>
            <p class="text-muted mb-0">تواصل مع فريقك 작업</p>
        </div>
    </div>
</div>
@endsection
