@extends('layouts.admin')
@section('title', 'إنشاء حملة تصويت')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-plus-circle text-primary"></i> إنشاء حملة تصويت جديدة</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.campaigns.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">عنوان الحملة <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">تاريخ النهاية</label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الجمهور المستهدف <span class="text-danger">*</span></label>
                    <select name="target_audience" class="form-select" id="targetAudience" required>
                        <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>جميع الأندية</option>
                        <option value="specific" {{ old('target_audience') == 'specific' ? 'selected' : '' }}>أندية محددة</option>
                    </select>
                </div>
                <div class="col-md-6" id="clubsSelection" style="display:none;">
                    <label class="form-label">اختر الأندية</label>
                    <div class="border rounded p-2" style="max-height:200px;overflow-y:auto;">
                        @foreach($clubs as $club)
                            <div class="form-check">
                                <input type="checkbox" name="target_clubs[]" value="{{ $club->id }}" class="form-check-input"
                                    {{ in_array($club->id, old('target_clubs', [])) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $club->name_ar }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">إظهار النتائج بعد</label>
                    <input type="datetime-local" name="results_visible_after" class="form-control" value="{{ old('results_visible_after') }}">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="single_response" class="form-check-input" id="single_response" checked>
                        <label class="form-check-label" for="single_response">إجابة واحدة فقط</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="prevent_edit_after_submit" class="form-check-input" id="prevent_edit" checked>
                        <label class="form-check-label" for="prevent_edit">منع التعديل بعد الإرسال</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="show_countdown" class="form-check-input" id="show_countdown" checked>
                        <label class="form-check-label" for="show_countdown">عرض العداد التنازلي</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="allow_review_before_submit" class="form-check-input" id="allow_review" checked>
                        <label class="form-check-label" for="allow_review">شاشة مراجعة قبل الإرسال</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> إنشاء وإضافة الأسئلة</button>
                <button type="submit" name="save_as_draft" value="1" class="btn btn-outline-secondary"><i class="bi bi-file-earmark"></i> حفظ كمسودة</button>
                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-danger">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('targetAudience').addEventListener('change', function() {
    document.getElementById('clubsSelection').style.display = this.value === 'specific' ? '' : 'none';
});
if(document.getElementById('targetAudience').value === 'specific') {
    document.getElementById('clubsSelection').style.display = '';
}
</script>
@endpush
@endsection
