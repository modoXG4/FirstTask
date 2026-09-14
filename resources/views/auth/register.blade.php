@extends('layouts.app')

@section('title', 'تسجيل حساب جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card mt-5">
            <div class="card-header text-center py-3">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>تسجيل حساب جديد</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">الاسم</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="الاسم الكامل">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required
                                   placeholder="example@email.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required
                                   placeholder="8 أحرف على الأقل">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">يجب أن تحتوي على 8 أحرف على الأقل</div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required
                                   placeholder="أعد إدخال كلمة المرور">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-user-plus me-2"></i>تسجيل
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <span class="text-muted">لديك حساب بالفعل؟</span>
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">سجّل دخولك</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
