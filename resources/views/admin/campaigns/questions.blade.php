@extends('layouts.admin')
@section('title', 'أسئلة الحملة')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1"><i class="bi bi-list-check text-primary"></i> أسئلة الحملة</h4>
        <p class="text-muted mb-0">{{ $campaign->title }}</p>
    </div>
    <div>
        <a href="{{ route('admin.campaigns.send', $campaign) }}" class="btn btn-success"><i class="bi bi-send"></i> إرسال الحملة</a>
        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary">رجوع</a>
    </div>
</div>

<!-- Existing Questions -->
@foreach($campaign->questions as $question)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-primary me-2">{{ $question->sort_order }}</span>
            <strong>{{ $question->title }}</strong>
            <span class="badge bg-{{ $question->type == 'radio' ? 'info' : 'warning' }} ms-2">{{ $question->type == 'radio' ? 'اختيار واحد' : 'اختيار متعدد' }}</span>
            @if($question->is_required) <span class="badge bg-danger ms-1">إجباري</span> @endif
        </div>
        <form method="POST" action="{{ route('admin.campaigns.questions.destroy', [$campaign, $question]) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
    </div>
    <div class="card-body">
        @if($question->description)
            <p class="text-muted small mb-2">{{ $question->description }}</p>
        @endif
        <div class="row">
            @foreach($question->options as $option)
            <div class="col-md-3 mb-2">
                <div class="border rounded p-2 text-center bg-light">
                    <small>{{ $option->label }}</small>
                </div>
            </div>
            @endforeach
        </div>
        @if($question->type == 'checkbox' && $question->max_selections)
            <small class="text-muted">الحد الأقصى للاختيارات: {{ $question->max_selections }}</small>
        @endif
    </div>
</div>
@endforeach

<!-- Add New Question -->
<div class="card border-0 shadow-sm border-primary" style="border-top: 3px solid #0d6efd !important;">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-plus-circle text-primary"></i> إضافة سؤال جديد</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.campaigns.questions.store', $campaign) }}" id="questionForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">عنوان السؤال <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">نوع السؤال <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" id="questionType" required>
                        <option value="radio">اختيار واحد (Radio)</option>
                        <option value="checkbox">اختيار متعدد (Checkbox)</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">وصف (اختياري)</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch mt-4">
                        <input type="checkbox" name="is_required" class="form-check-input" id="isRequired" checked>
                        <label class="form-check-label" for="isRequired">إجباري</label>
                    </div>
                </div>
                <div class="col-md-4" id="maxSelectionsDiv" style="display:none;">
                    <label class="form-label">الحد الأقصى للاختيارات</label>
                    <input type="number" name="max_selections" class="form-control" min="1">
                </div>
            </div>

            <hr>
            <h6><i class="bi bi-list-ul"></i> الخيارات</h6>
            <div id="optionsContainer">
                <div class="row g-2 mb-2 option-row align-items-center">
                    <div class="col">
                        <input type="text" name="options[0][label]" class="form-control" placeholder="اكتب نص الخيار هنا..." required>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger btn-remove-option" disabled><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                <div class="row g-2 mb-2 option-row align-items-center">
                    <div class="col">
                        <input type="text" name="options[1][label]" class="form-control" placeholder="اكتب نص الخيار هنا..." required>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger btn-remove-option" disabled><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addOption">
                <i class="bi bi-plus"></i> إضافة خيار
            </button>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> إضافة السؤال</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let optionIndex = 2;
document.getElementById('addOption').addEventListener('click', function() {
    const container = document.getElementById('optionsContainer');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 option-row align-items-center';
    row.innerHTML = `
        <div class="col">
            <input type="text" name="options[${optionIndex}][label]" class="form-control" placeholder="اكتب نص الخيار هنا..." required>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-danger btn-remove-option" onclick="this.closest('.option-row').remove();updateRemoveButtons();"><i class="bi bi-x-lg"></i></button>
        </div>
    `;
    container.appendChild(row);
    optionIndex++;
    updateRemoveButtons();
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.option-row');
    rows.forEach(row => {
        const btn = row.querySelector('.btn-remove-option');
        btn.disabled = rows.length <= 2;
        btn.onclick = function() { row.remove(); updateRemoveButtons(); };
    });
}

document.getElementById('questionType').addEventListener('change', function() {
    document.getElementById('maxSelectionsDiv').style.display = this.value === 'checkbox' ? '' : 'none';
});
</script>
@endpush
@endsection
