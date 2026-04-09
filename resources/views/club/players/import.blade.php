@extends('layouts.admin')
@section('title', 'استيراد لاعبين')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-upload text-primary"></i> استيراد لاعبين من ملف</h4>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('club.players.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اختر ملف Excel أو CSV</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المدعومة: xlsx, xls, csv</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> استيراد</button>
                    <a href="{{ route('club.players.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">تنسيق الملف المطلوب</h6></div>
            <div class="card-body">
                <p class="small">يجب أن يحتوي الملف على الأعمدة التالية:</p>
                <table class="table table-sm">
                    <thead><tr><th>العمود (EN)</th><th>العمود (AR)</th></tr></thead>
                    <tbody>
                        <tr><td>name</td><td>الاسم</td></tr>
                        <tr><td>national_id</td><td>رقم_الهوية</td></tr>
                        <tr><td>player_number</td><td>رقم_اللاعب</td></tr>
                        <tr><td>phone</td><td>الجوال</td></tr>
                        <tr><td>email</td><td>البريد</td></tr>
                        <tr><td>nationality</td><td>الجنسية</td></tr>
                        <tr><td>position</td><td>المركز</td></tr>
                    </tbody>
                </table>
                <p class="small text-muted">يمكن استخدام أي من اللغتين للأعمدة</p>
            </div>
        </div>
    </div>
</div>
@endsection
