@extends('layouts.app')

@section('title', __('ui.projects.create_title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="page-header d-flex align-items-center">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="fas fa-arrow-right"></i>
            </a>
            <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>{{ __('ui.projects.create_title') }}</h2>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">{{ __('ui.projects.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="{{ __('ui.projects.name_placeholder') }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">{{ __('ui.projects.description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="{{ __('ui.projects.description_placeholder') }}">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('ui.common.save') }}
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('ui.common.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
