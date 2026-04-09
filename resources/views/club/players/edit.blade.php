@extends('layouts.admin')
@section('title', 'تعديل اللاعب')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil text-primary"></i> تعديل اللاعب: {{ $player->name }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('club.players.update', $player) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $player->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهوية</label>
                    <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $player->national_id) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">رقم اللاعب</label>
                    <input type="text" name="player_number" class="form-control" value="{{ old('player_number', $player->player_number) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الجوال</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $player->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $player->email) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الجنسية</label>
                    <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $player->nationality) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">المركز</label>
                    <input type="text" name="position" class="form-control" value="{{ old('position', $player->position) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select" required>
                        @foreach(['active' => 'نشط', 'suspended' => 'موقوف', 'injured' => 'مصاب', 'retired' => 'معتزل', 'inactive' => 'غير مفعل'] as $val => $label)
                            <option value="{{ $val }}" {{ $player->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if($player->photo)
                        <div class="mt-2"><img src="{{ Storage::url($player->photo) }}" alt="" width="60" class="rounded"></div>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> تحديث</button>
                <a href="{{ route('club.players.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
