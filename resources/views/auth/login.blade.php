@extends('layouts.app')

@section('title', 'تسجيل دخول')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card mt-5">
            <div class="card-header text-center py-3">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>تسجيل دخول</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus
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
                                   placeholder="••••••••">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-sign-in-alt me-2"></i>دخول
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <span class="text-muted">ليس لديك حساب؟</span>
                    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">سجل الآن</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        var password = document.getElementById('password');
        var icon = this.querySelector('i');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
@endsection
