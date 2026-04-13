@extends('layouts.guest')
@section('title', 'تسجيل الدخول')
@section('heading', 'منصة التصويت الرياضي')
@section('subheading', 'تسجيل الدخول للوحة التحكم')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold">البريد الإلكتروني</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="example@email.com">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">كلمة المرور</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" required placeholder="********">
        </div>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">تذكرني</label>
    </div>
    <button type="submit" class="btn btn-auth">
        <i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول
    </button>
</form>

@if(Route::has('password.request'))
<div class="text-center mt-3">
    <a href="{{ route('password.request') }}" class="auth-link small">نسيت كلمة المرور؟</a>
</div>
@endif
@endsection
