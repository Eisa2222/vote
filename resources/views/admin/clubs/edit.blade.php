@extends('layouts.admin')
@section('title', 'تعديل النادي')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil text-primary"></i> تعديل النادي: {{ $club->name_ar }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.clubs.update', $club) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الدوري <span class="text-danger">*</span></label>
                    <select name="league_id" class="form-select" required>
                        <option value="">اختر الدوري</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}" {{ old('league_id', $club->league_id) == $league->id ? 'selected' : '' }}>
                                {{ $league->name_ar }} @if($league->season) ({{ $league->season }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $club->name_ar) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $club->name_en) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $club->contact_email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الجوال</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $club->contact_phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الشعار</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    @if($club->logo)
                        <div class="mt-2"><img src="{{ Storage::url($club->logo) }}" alt="" width="60" class="rounded"></div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $club->address) }}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $club->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> تحديث</button>
                <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
