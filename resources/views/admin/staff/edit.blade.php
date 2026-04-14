@extends('layouts.admin')
@section('title', 'تعديل موظف')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil text-primary"></i> تعديل موظف: {{ $user->name }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.staff.update', $user) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الجوال</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور <small class="text-muted">(اختياري)</small></label>
                    <input type="password" name="password" class="form-control" minlength="8">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الدور الوظيفي <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="campaign-creator" {{ $currentRole == 'campaign-creator' ? 'selected' : '' }}>منشئ التصويت</option>
                        <option value="campaign-reviewer" {{ $currentRole == 'campaign-reviewer' ? 'selected' : '' }}>مراجع التصويت</option>
                        <option value="campaign-approver" {{ $currentRole == 'campaign-approver' ? 'selected' : '' }}>معتمد التصويت</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> تحديث</button>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
