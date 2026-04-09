@extends('layouts.admin')
@section('title', 'إضافة لاعب')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person-plus text-primary"></i> إضافة لاعب جديد</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('club.players.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهوية</label>
                    <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">رقم اللاعب</label>
                    <input type="text" name="player_number" class="form-control" value="{{ old('player_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الجوال</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الجنسية</label>
                    <input type="text" name="nationality" class="form-control" value="{{ old('nationality') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">المركز</label>
                    <input type="text" name="position" class="form-control" value="{{ old('position') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select" required>
                        <option value="active">نشط</option>
                        <option value="suspended">موقوف</option>
                        <option value="injured">مصاب</option>
                        <option value="retired">معتزل</option>
                        <option value="inactive">غير مفعل</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ</button>
                <a href="{{ route('club.players.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
