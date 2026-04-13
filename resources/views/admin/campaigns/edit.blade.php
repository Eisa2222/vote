@extends('layouts.admin')
@section('title', 'تعديل الحملة')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-pencil text-primary"></i> تعديل الحملة: {{ $campaign->title }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">عنوان الحملة <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $campaign->description) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $campaign->starts_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">تاريخ النهاية</label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ $campaign->status == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="scheduled" {{ $campaign->status == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                        <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="closed" {{ $campaign->status == 'closed' ? 'selected' : '' }}>مغلقة</option>
                        <option value="archived" {{ $campaign->status == 'archived' ? 'selected' : '' }}>مؤرشفة</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">نوع التصويت</label>
                    <select name="voting_type" class="form-select">
                        <option value="public" {{ $campaign->voting_type == 'public' ? 'selected' : '' }}>تصويت عام</option>
                        <option value="private" {{ $campaign->voting_type == 'private' ? 'selected' : '' }}>تصويت خاص</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الجمهور المستهدف</label>
                    <select name="target_audience" class="form-select" id="targetAudience" required>
                        <option value="all" {{ $campaign->target_audience == 'all' ? 'selected' : '' }}>جميع الأندية</option>
                        <option value="specific" {{ $campaign->target_audience == 'specific' ? 'selected' : '' }}>أندية محددة</option>
                    </select>
                </div>
                <div class="col-md-6" id="clubsSelection" style="{{ $campaign->target_audience == 'specific' ? '' : 'display:none;' }}">
                    <label class="form-label">اختر الأندية</label>
                    <div class="border rounded p-2" style="max-height:200px;overflow-y:auto;">
                        @foreach($clubs as $club)
                            <div class="form-check">
                                <input type="checkbox" name="target_clubs[]" value="{{ $club->id }}" class="form-check-input"
                                    {{ in_array($club->id, old('target_clubs', $targetClubIds)) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $club->name_ar }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="single_response" class="form-check-input" {{ $campaign->single_response ? 'checked' : '' }}>
                        <label class="form-check-label">إجابة واحدة فقط</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="prevent_edit_after_submit" class="form-check-input" {{ $campaign->prevent_edit_after_submit ? 'checked' : '' }}>
                        <label class="form-check-label">منع التعديل بعد الإرسال</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="show_countdown" class="form-check-input" {{ $campaign->show_countdown ? 'checked' : '' }}>
                        <label class="form-check-label">عرض العداد التنازلي</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="allow_review_before_submit" class="form-check-input" {{ $campaign->allow_review_before_submit ? 'checked' : '' }}>
                        <label class="form-check-label">شاشة مراجعة</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> تحديث</button>
                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('targetAudience').addEventListener('change', function() {
    document.getElementById('clubsSelection').style.display = this.value === 'specific' ? '' : 'none';
});
</script>
@endpush
@endsection
