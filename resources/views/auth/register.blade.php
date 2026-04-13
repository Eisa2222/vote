@extends('layouts.guest')
@section('title', 'التسجيل مغلق')
@section('heading', 'التسجيل مغلق')

@section('content')
<div class="alert alert-warning text-center">
    <i class="bi bi-shield-lock fs-1 d-block mb-2"></i>
    <h5>التسجيل العام مغلق</h5>
    <p class="small mb-0">الحسابات تُنشأ من قِبل مدير الجمعية فقط. يُرجى التواصل مع الإدارة للحصول على حساب.</p>
</div>

<a href="{{ route('login') }}" class="btn btn-auth">
    <i class="bi bi-box-arrow-in-left"></i> العودة لتسجيل الدخول
</a>
@endsection
