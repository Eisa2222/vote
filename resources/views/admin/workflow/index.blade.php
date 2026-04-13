@extends('layouts.admin')
@section('title', 'سير العمل والاعتماد')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-diagram-3 text-primary"></i> سير العمل والاعتماد</h4>
</div>

<!-- Workflow Steps Info -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between text-center flex-wrap gap-1">
            <div><span class="badge bg-secondary px-3 py-2">1. مسودة</span><br><small class="text-muted">المنشئ</small></div>
            <div class="align-self-center text-muted"><i class="bi bi-arrow-left"></i></div>
            <div><span class="badge bg-warning px-3 py-2">2. بانتظار المراجعة</span><br><small class="text-muted">المراجع</small></div>
            <div class="align-self-center text-muted"><i class="bi bi-arrow-left"></i></div>
            <div><span class="badge bg-primary px-3 py-2">3. بانتظار الاعتماد</span><br><small class="text-muted">المعتمد</small></div>
            <div class="align-self-center text-muted"><i class="bi bi-arrow-left"></i></div>
            <div><span class="badge bg-success px-3 py-2">4. معتمدة</span><br><small class="text-muted">جاهزة للإرسال</small></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Pending Review -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 border-0">
                <h6 class="mb-0"><i class="bi bi-hourglass-split text-warning"></i> بانتظار المراجعة ({{ $pendingReview->count() }})</h6>
            </div>
            <div class="card-body p-0">
                @forelse($pendingReview as $c)
                <a href="{{ route('admin.workflow.show', $c) }}" class="d-block p-3 border-bottom text-decoration-none text-dark hover-bg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $c->title }}</strong>
                            <div class="small text-muted"><i class="bi bi-list-check"></i> {{ $c->questions_count }} سؤال | {{ $c->created_at->format('Y-m-d') }}</div>
                        </div>
                        <span class="badge bg-warning">مراجعة</span>
                    </div>
                </a>
                @empty
                <div class="p-3 text-center text-muted small">لا توجد حملات بانتظار المراجعة</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Approval -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary bg-opacity-10 border-0">
                <h6 class="mb-0"><i class="bi bi-shield-check text-primary"></i> بانتظار الاعتماد ({{ $pendingApproval->count() }})</h6>
            </div>
            <div class="card-body p-0">
                @forelse($pendingApproval as $c)
                <a href="{{ route('admin.workflow.show', $c) }}" class="d-block p-3 border-bottom text-decoration-none text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $c->title }}</strong>
                            <div class="small text-muted"><i class="bi bi-list-check"></i> {{ $c->questions_count }} سؤال | {{ $c->created_at->format('Y-m-d') }}</div>
                        </div>
                        <span class="badge bg-primary">اعتماد</span>
                    </div>
                </a>
                @empty
                <div class="p-3 text-center text-muted small">لا توجد حملات بانتظار الاعتماد</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Actions -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-clock-history text-info"></i> آخر الإجراءات</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>التاريخ</th><th>المستخدم</th><th>الحملة</th><th>الإجراء</th><th>ملاحظة</th></tr></thead>
                <tbody>
                    @forelse($recentActions as $action)
                    <tr>
                        <td class="small text-muted">{{ $action->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $action->user->name ?? '-' }}</td>
                        <td><a href="{{ route('admin.workflow.show', $action->campaign_id) }}">{{ $action->campaign->title ?? '-' }}</a></td>
                        <td><span class="badge bg-info">{{ $action->action_label }}</span></td>
                        <td class="small">{{ $action->comment ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">لا توجد إجراءات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
