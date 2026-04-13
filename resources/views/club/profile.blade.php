@extends('layouts.admin')
@section('title', 'ملف النادي')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-building text-primary"></i> ملف النادي</h4>
</div>

@php $club = auth()->user()->club; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-4">
                @if($club->logo)
                    <img src="{{ Storage::url($club->logo) }}" alt="" width="100" class="rounded-circle mb-3">
                @else
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;">
                        <i class="bi bi-building fs-1 text-primary"></i>
                    </div>
                @endif
                <h5>{{ $club->name_ar }}</h5>
                @if($club->name_en) <p class="text-muted mb-1">{{ $club->name_en }}</p> @endif
                @if($club->league)
                    <span class="badge bg-primary">{{ $club->league->name_ar }}</span>
                @endif
                <div class="mt-2">
                    @if($club->is_active)
                        <span class="badge bg-success">نشط</span>
                    @else
                        <span class="badge bg-danger">معطل</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">بيانات النادي</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="150">البريد الإلكتروني</td><td>{{ $club->contact_email ?? '-' }}</td></tr>
                    <tr><td class="text-muted">الجوال</td><td>{{ $club->contact_phone ?? '-' }}</td></tr>
                    <tr><td class="text-muted">العنوان</td><td>{{ $club->address ?? '-' }}</td></tr>
                    <tr><td class="text-muted">الدوري</td><td>{{ $club->league->name_ar ?? '-' }} {{ $club->league->season ? '(' . $club->league->season . ')' : '' }}</td></tr>
                    <tr><td class="text-muted">عدد اللاعبين</td><td><span class="badge bg-info">{{ $club->players()->count() }}</span></td></tr>
                    <tr><td class="text-muted">تاريخ التسجيل</td><td>{{ $club->created_at->format('Y-m-d') }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><h6 class="mb-0">بيانات الإداري</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="150">الاسم</td><td>{{ auth()->user()->name }}</td></tr>
                    <tr><td class="text-muted">البريد</td><td>{{ auth()->user()->email }}</td></tr>
                    <tr><td class="text-muted">الجوال</td><td>{{ auth()->user()->phone ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
