@extends('layouts.guest')
@section('title', 'نسيت كلمة المرور')
@section('heading', 'استعادة كلمة المرور')
@section('subheading', 'أدخل بريدك وسنرسل لك رابط إعادة التعيين')

@section('content')
<div class="alert alert-info small">
    <i class="bi bi-info-circle"></i>
    نسيت كلمة المرور؟ أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة التعيين.
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold">البريد الإلكتروني</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="example@email.com">
        </div>
    </div>
    <button type="submit" class="btn btn-auth">
        <i class="bi bi-send"></i> إرسال رابط الاستعادة
    </button>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="auth-link small">
        <i class="bi bi-arrow-right"></i> العودة لتسجيل الدخول
    </a>
</div>
@endsection
