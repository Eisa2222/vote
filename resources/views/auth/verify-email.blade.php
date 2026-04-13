@extends('layouts.guest')
@section('title', 'تأكيد البريد الإلكتروني')
@section('heading', 'تأكيد البريد الإلكتروني')

@section('content')
<div class="alert alert-info small">
    <i class="bi bi-envelope-check"></i>
    شكراً لتسجيلك. قبل البدء، يُرجى تأكيد بريدك الإلكتروني بالضغط على الرابط المرسل إليك.
    إذا لم تستلم البريد، يمكنك طلب إرسال رابط جديد.
</div>

@if(session('status') == 'verification-link-sent')
<div class="alert alert-success small">
    <i class="bi bi-check-circle"></i>
    تم إرسال رابط تأكيد جديد إلى بريدك الإلكتروني.
</div>
@endif

<form method="POST" action="{{ route('verification.send') }}" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-auth">
        <i class="bi bi-envelope"></i> إعادة إرسال رابط التأكيد
    </button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-link auth-link w-100 small">
        <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
    </button>
</form>
@endsection
