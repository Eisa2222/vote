@extends('layouts.admin')
@section('title', 'تعديل الدوري')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil text-primary"></i> تعديل الدوري: {{ $league->name_ar }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.leagues.update', $league) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $league->name_ar) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $league->name_en) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الموسم</label>
                    <input type="text" name="season" class="form-control" value="{{ old('season', $league->season) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الشعار</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    @if($league->logo)
                        <div class="mt-2"><img src="{{ Storage::url($league->logo) }}" alt="" width="50" class="rounded"></div>
                    @endif
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $league->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $league->description) }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> تحديث</button>
                <a href="{{ route('admin.leagues.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
