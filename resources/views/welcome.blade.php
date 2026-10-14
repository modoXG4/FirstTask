@extends('layouts.app')

@section('title', __('ui.welcome.title'))

@push('styles')
<script>document.documentElement.classList.add('js');</script>
<style>
    .hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 4.5rem 1.5rem 6rem;
        color: #fff;
        background: linear-gradient(-45deg, #6366f1, #8b5cf6, #ec4899, #3b82f6);
        background-size: 300% 300%;
        animation: heroGradient 16s ease infinite;
        box-shadow: 0 25px 60px -25px rgba(99, 102, 241, 0.75);
    }

    @keyframes heroGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.25) 1px, transparent 1px);
        background-size: 26px 26px;
        opacity: 0.35;
        pointer-events: none;
    }

    .hero-blobs {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
    }

    .hero-blobs span {
        position: absolute;
        border-radius: 50%;
        filter: blur(50px);
        opacity: 0.5;
    }

    .hero-blobs .b1 { width: 260px; height: 260px; background: #a78bfa; top: -60px; left: -40px; animation: float1 14s ease-in-out infinite; }
    .hero-blobs .b2 { width: 220px; height: 220px; background: #f472b6; bottom: -50px; right: -30px; animation: float2 16s ease-in-out infinite; }
    .hero-blobs .b3 { width: 180px; height: 180px; background: #60a5fa; top: 30%; right: 15%; animation: float1 18s ease-in-out infinite; }
    .hero-blobs .b4 { width: 160px; height: 160px; background: #34d399; bottom: 10%; left: 20%; animation: float2 20s ease-in-out infinite; }

    @keyframes float1 {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(40px, -30px); }
    }

    @keyframes float2 {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-35px, 25px); }
    }

    .hero-content { position: relative; z-index: 2; }

    .hero-badge {
        display: inline-block;
        margin-bottom: 1.25rem;
        padding: 0.4rem 1rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        backdrop-filter: blur(6px);
        font-size: 0.85rem;
        font-weight: 600;
        animation: fadeUp 0.7s ease both;
    }

    .hero-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        animation: floaty 4s ease-in-out infinite;
        text-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    }

    @keyframes floaty {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .hero-title {
        font-size: clamp(2rem, 5vw, 3.4rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        animation: fadeUp 0.7s 0.1s ease both;
    }

    .hero-sub {
        font-size: 1.15rem;
        opacity: 0.92;
        max-width: 620px;
        margin: 0 auto 2rem;
        animation: fadeUp 0.7s 0.2s ease both;
    }

    .hero-actions { animation: fadeUp 0.7s 0.3s ease both; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(26px); }
        to { opacity: 1; transform: none; }
    }

    .hero-btn {
        padding: 0.85rem 1.9rem;
        border-radius: 999px;
        font-weight: 600;
        border: 0;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .hero-btn-primary { background: #fff; color: #4f46e5; box-shadow: 0 10px 25px -10px rgba(0, 0, 0, 0.5); }
    .hero-btn-primary:hover { transform: translateY(-3px); box-shadow: 0 16px 30px -12px rgba(0, 0, 0, 0.6); color: #4338ca; }
    .hero-btn-outline { background: rgba(255, 255, 255, 0.12); color: #fff; border: 1.5px solid rgba(255, 255, 255, 0.6); backdrop-filter: blur(6px); }
    .hero-btn-outline:hover { background: rgba(255, 255, 255, 0.22); transform: translateY(-3px); color: #fff; }

    .hero-wave { position: absolute; left: 0; right: 0; bottom: -1px; line-height: 0; z-index: 1; }
    .hero-wave svg { display: block; width: 100%; height: 90px; }

    .section-heading { font-weight: 700; margin-bottom: 0.4rem; }

    .feature-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 30px -18px rgba(15, 23, 42, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-card:hover { transform: translateY(-6px); box-shadow: 0 22px 45px -22px rgba(15, 23, 42, 0.45); }

    .feature-icon {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .js .reveal {
        opacity: 0;
        transform: translateY(34px);
        transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.2, 0.7, 0.2, 1);
    }

    .js .reveal.is-visible { opacity: 1; transform: none; }

    .cta-band {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 3.5rem 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.7), transparent 45%),
            radial-gradient(circle at 85% 25%, rgba(236, 72, 153, 0.6), transparent 45%),
            radial-gradient(circle at 50% 100%, rgba(59, 130, 246, 0.6), transparent 50%),
            linear-gradient(135deg, #0f172a, #1e293b);
        box-shadow: 0 25px 50px -25px rgba(15, 23, 42, 0.85);
    }

    .cta-band::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.5;
        animation: ctaPan 30s linear infinite;
    }

    @keyframes ctaPan {
        from { background-position: 0 0; }
        to { background-position: 240px 240px; }
    }

    .cta-inner { position: relative; z-index: 2; }

    .cta-play {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: #fff;
        font-size: 1.3rem;
        margin-bottom: 1.25rem;
        animation: ctaPulse 2s ease-in-out infinite;
        backdrop-filter: blur(4px);
    }

    @keyframes ctaPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); transform: scale(1); }
        50% { box-shadow: 0 0 0 18px rgba(255, 255, 255, 0); transform: scale(1.05); }
    }
</style>
@endpush

@section('content')
<div class="hero">
    <div class="hero-blobs">
        <span class="b1"></span>
        <span class="b2"></span>
        <span class="b3"></span>
        <span class="b4"></span>
    </div>

    <div class="hero-content text-center">
        <span class="hero-badge">{{ __('ui.welcome.badge') }}</span>
        <div class="hero-icon">
            <i class="fas fa-tasks"></i>
        </div>
        <h1 class="hero-title mb-3">{{ __('ui.welcome.hero_title') }}</h1>
        <p class="hero-sub">{{ __('ui.welcome.hero_sub') }}</p>

        <div class="hero-actions d-flex justify-content-center gap-3 flex-wrap">
            @auth
            <a href="{{ route('projects.index') }}" class="btn hero-btn hero-btn-primary">
                <i class="fas fa-folder-open me-2"></i>{{ __('ui.welcome.go_projects') }}
            </a>
            @else
            <a href="{{ route('login') }}" class="btn hero-btn hero-btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>{{ __('ui.welcome.login') }}
            </a>
            <a href="{{ route('register') }}" class="btn hero-btn hero-btn-outline">
                <i class="fas fa-user-plus me-2"></i>{{ __('ui.welcome.register') }}
            </a>
            @endauth
        </div>
    </div>

    <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="var(--bg-main)" d="M0,64 C240,120 480,0 720,32 C960,64 1200,120 1440,64 L1440,120 L0,120 Z"></path>
        </svg>
    </div>
</div>

<div class="text-center mt-5 mb-4 reveal">
    <h3 class="section-heading">{{ __('ui.welcome.features_heading') }}</h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-4 mb-4 reveal">
        <div class="card feature-card text-center p-4 h-100">
            <div class="mb-3">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
            <h5 class="fw-bold">{{ __('ui.welcome.features.projects_title') }}</h5>
            <p class="text-muted mb-0">{{ __('ui.welcome.features.projects_desc') }}</p>
        </div>
    </div>
    <div class="col-md-4 mb-4 reveal">
        <div class="card feature-card text-center p-4 h-100">
            <div class="mb-3">
                <div class="feature-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <h5 class="fw-bold">{{ __('ui.welcome.features.tasks_title') }}</h5>
            <p class="text-muted mb-0">{{ __('ui.welcome.features.tasks_desc') }}</p>
        </div>
    </div>
    <div class="col-md-4 mb-4 reveal">
        <div class="card feature-card text-center p-4 h-100">
            <div class="mb-3">
                <div class="feature-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <h5 class="fw-bold">{{ __('ui.welcome.features.comments_title') }}</h5>
            <p class="text-muted mb-0">{{ __('ui.welcome.features.comments_desc') }}</p>
        </div>
    </div>
</div>

<div class="cta-band mt-4 reveal">
    <div class="cta-inner text-center">
        <div class="cta-play">
            <i class="fas fa-play"></i>
        </div>
        <h3 class="fw-bold mb-2">{{ __('ui.welcome.cta_title') }}</h3>
        <p class="mb-4 mx-auto" style="opacity: 0.85; max-width: 520px;">{{ __('ui.welcome.cta_sub') }}</p>
        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
            @auth
            <a href="{{ route('projects.index') }}" class="btn btn-light btn-lg fw-semibold px-4">
                <i class="fas fa-folder-open me-2"></i>{{ __('ui.welcome.go_projects') }}
            </a>
            @else
            <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold px-4">
                <i class="fas fa-rocket me-2"></i>{{ __('ui.welcome.cta_button') }}
            </a>
            @endauth
            <small class="text-white-50">
                <i class="fas fa-circle-play me-1"></i>{{ __('ui.welcome.watch_demo') }}
            </small>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var revealEls = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach(function (el) { observer.observe(el); });
    });
</script>
@endsection
