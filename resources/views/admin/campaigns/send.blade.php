@extends('layouts.admin')
@section('title', 'إرسال الحملة')

@section('content')
<div class="page-header">
    <h4 class="mb-1"><i class="bi bi-send text-primary"></i> إرسال الحملة</h4>
    <p class="text-muted mb-0">{{ $campaign->title }}</p>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6>معلومات الحملة</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>الحالة:</strong>
                        @switch($campaign->status)
                            @case('draft') <span class="badge bg-secondary">مسودة</span> @break
                            @case('active') <span class="badge bg-success">نشطة</span> @break
                            @default <span class="badge bg-info">{{ $campaign->status }}</span>
                        @endswitch
                    </li>
                    <li class="mb-2"><strong>البداية:</strong> {{ $campaign->starts_at?->format('Y-m-d H:i') ?? 'غير محدد' }}</li>
                    <li class="mb-2"><strong>النهاية:</strong> {{ $campaign->ends_at?->format('Y-m-d H:i') ?? 'غير محدد' }}</li>
                    <li class="mb-2"><strong>عدد الأسئلة:</strong> {{ $campaign->questions->count() }}</li>
                    <li><strong>الأندية المستهدفة:</strong> {{ $campaign->targets->count() }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">اختر الإداريين للإرسال</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.campaigns.send-to-admins', $campaign) }}">
                    @csrf
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="document.querySelectorAll('.admin-check').forEach(c=>c.checked=this.checked)">
                            <label class="form-check-label fw-bold" for="selectAll">تحديد الكل</label>
                        </div>
                        <hr>
                        @foreach($admins as $admin)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="admin_ids[]" value="{{ $admin->id }}" class="form-check-input admin-check"
                                {{ in_array($admin->id, $existingAssignments) ? 'checked disabled' : '' }}>
                            <label class="form-check-label">
                                {{ $admin->name }} - <span class="text-muted">{{ $admin->club->name_ar ?? '' }}</span>
                                @if(in_array($admin->id, $existingAssignments))
                                    <span class="badge bg-success ms-2">تم الإرسال</span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> إرسال للإداريين المحددين</button>
                    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary">رجوع</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
