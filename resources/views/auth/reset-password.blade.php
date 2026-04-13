@extends('layouts.guest')
@section('title', 'إعادة تعيين كلمة المرور')
@section('heading', 'إعادة تعيين كلمة المرور')
@section('subheading', 'أدخل كلمة المرور الجديدة')

@section('content')
<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="mb-3">
        <label class="form-label fw-bold">البريد الإلكتروني</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">كلمة المرور الجديدة</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" required autocomplete="new-password" placeholder="********">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">تأكيد كلمة المرور</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="********">
        </div>
    </div>

    <button type="submit" class="btn btn-auth">
        <i class="bi bi-check-circle"></i> إعادة تعيين كلمة المرور
    </button>
</form>
@endsection
