@extends('layouts.guest')
@section('title', 'تأكيد كلمة المرور')
@section('heading', 'تأكيد كلمة المرور')
@section('subheading', 'منطقة آمنة - يُرجى تأكيد كلمة المرور للمتابعة')

@section('content')
<div class="alert alert-warning small">
    <i class="bi bi-shield-lock"></i>
    هذه منطقة آمنة من التطبيق. يُرجى تأكيد كلمة المرور قبل المتابعة.
</div>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold">كلمة المرور</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" required autocomplete="current-password" autofocus placeholder="********">
        </div>
    </div>
    <button type="submit" class="btn btn-auth">
        <i class="bi bi-check-lg"></i> تأكيد
    </button>
</form>
@endsection
