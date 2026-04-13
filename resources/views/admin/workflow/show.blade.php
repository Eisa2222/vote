@extends('layouts.admin')
@section('title', 'مراجعة الحملة')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1"><i class="bi bi-diagram-3 text-primary"></i> {{ $campaign->title }}</h4>
        <span class="badge bg-{{ $campaign->workflow_status_color }} fs-6">{{ $campaign->workflow_status_label }}</span>
    </div>
    <a href="{{ route('admin.workflow.index') }}" class="btn btn-outline-secondary">رجوع</a>
</div>

<div class="row g-3">
    <!-- Campaign Info -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0">بيانات الحملة</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="150">العنوان</td><td>{{ $campaign->title }}</td></tr>
                    <tr><td class="text-muted">الوصف</td><td>{{ $campaign->description ?? '-' }}</td></tr>
                    <tr><td class="text-muted">النوع</td><td>{{ $campaign->voting_type === 'public' ? 'عام' : 'خاص' }}</td></tr>
                    <tr><td class="text-muted">البداية</td><td>{{ $campaign->starts_at?->format('Y-m-d H:i') ?? '-' }}</td></tr>
                    <tr><td class="text-muted">النهاية</td><td>{{ $campaign->ends_at?->format('Y-m-d H:i') ?? '-' }}</td></tr>
                    <tr><td class="text-muted">المنشئ</td><td>{{ $campaign->creator->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted">الأندية المستهدفة</td><td>
                        @foreach($campaign->targets as $t)
                            <span class="badge bg-light text-dark">{{ $t->club->name_ar ?? '' }}</span>
                        @endforeach
                    </td></tr>
                </table>
            </div>
        </div>

        <!-- Questions -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0">الأسئلة ({{ $campaign->questions->count() }})</h6></div>
            <div class="card-body">
                @foreach($campaign->questions as $q)
                <div class="p-3 mb-2 bg-light rounded">
                    <strong>{{ $q->sort_order }}. {{ $q->title }}</strong>
                    @if($q->is_required) <span class="badge bg-danger">إجباري</span> @endif
                    <span class="badge bg-{{ $q->type == 'radio' ? 'info' : 'warning' }}">{{ $q->type == 'radio' ? 'اختيار واحد' : 'اختيار متعدد' }}</span>
                    @if($q->description) <div class="small text-muted mt-1">{{ $q->description }}</div> @endif
                    <div class="mt-2">
                        @foreach($q->options as $opt)
                            <span class="badge bg-white text-dark border me-1">{{ $opt->label }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actions Panel -->
    <div class="col-md-4">
        <!-- Workflow Actions -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-lightning text-warning"></i> إجراءات سير العمل</h6></div>
            <div class="card-body">
                @php $ws = $campaign->workflow_status; $user = auth()->user(); @endphp

                {{-- Creator: submit for review --}}
                @if($ws === 'draft' || $ws === 'rejected')
                    @if($user->hasAnyRole(['super-admin', 'campaign-creator']))
                    <form method="POST" action="{{ route('admin.workflow.submit-review', $campaign) }}" class="mb-2">
                        @csrf
                        <button class="btn btn-warning w-100"><i class="bi bi-send"></i> تقديم للمراجعة</button>
                    </form>
                    @endif
                @endif

                {{-- Reviewer: approve or reject --}}
                @if($ws === 'pending_review')
                    @if($user->hasAnyRole(['super-admin', 'campaign-reviewer']))
                    <form method="POST" action="{{ route('admin.workflow.approve-review', $campaign) }}" class="mb-2">
                        @csrf
                        <textarea name="comment" class="form-control form-control-sm mb-2" placeholder="ملاحظات المراجعة (اختياري)" rows="2"></textarea>
                        <button class="btn btn-success w-100"><i class="bi bi-check-circle"></i> اعتماد المراجعة</button>
                    </form>
                    <form method="POST" action="{{ route('admin.workflow.reject-review', $campaign) }}">
                        @csrf
                        <textarea name="comment" class="form-control form-control-sm mb-2" placeholder="سبب الرفض (مطلوب)" rows="2" required></textarea>
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> رفض</button>
                    </form>
                    @else
                    <div class="alert alert-info small mb-0"><i class="bi bi-hourglass"></i> بانتظار مراجعة المراجع المختص</div>
                    @endif
                @endif

                {{-- Approver: final approve or reject --}}
                @if($ws === 'pending_approval')
                    @if($user->hasAnyRole(['super-admin', 'campaign-approver']))
                    <form method="POST" action="{{ route('admin.workflow.approve-final', $campaign) }}" class="mb-2">
                        @csrf
                        <textarea name="comment" class="form-control form-control-sm mb-2" placeholder="ملاحظات الاعتماد (اختياري)" rows="2"></textarea>
                        <button class="btn btn-primary w-100"><i class="bi bi-shield-check"></i> الاعتماد النهائي</button>
                    </form>
                    <form method="POST" action="{{ route('admin.workflow.reject-final', $campaign) }}">
                        @csrf
                        <textarea name="comment" class="form-control form-control-sm mb-2" placeholder="سبب الرفض (مطلوب)" rows="2" required></textarea>
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> رفض</button>
                    </form>
                    @else
                    <div class="alert alert-info small mb-0"><i class="bi bi-hourglass"></i> بانتظار الاعتماد النهائي</div>
                    @endif
                @endif

                {{-- Approved: ready to send --}}
                @if($ws === 'approved')
                    <div class="alert alert-success small mb-2"><i class="bi bi-check-circle-fill"></i> الحملة معتمدة وجاهزة للإرسال</div>
                    @if($user->hasAnyRole(['super-admin', 'campaign-approver']))
                    <a href="{{ route('admin.campaigns.send', $campaign) }}" class="btn btn-success w-100"><i class="bi bi-send"></i> إرسال للأندية</a>
                    @endif
                @endif

                @if($ws === 'sent')
                    <div class="alert alert-dark small mb-0"><i class="bi bi-check-all"></i> تم إرسال الحملة</div>
                @endif
            </div>
        </div>

        <!-- Status Trail -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-person-lines-fill"></i> مسار الاعتماد</h6></div>
            <div class="card-body">
                <div class="mb-2"><small class="text-muted">المنشئ:</small> <strong>{{ $campaign->creator->name ?? '-' }}</strong></div>
                @if($campaign->submitted_at)
                <div class="mb-2"><small class="text-muted">قُدمت للمراجعة:</small> {{ $campaign->submitted_at->format('Y-m-d H:i') }}</div>
                @endif
                @if($campaign->reviewer)
                <div class="mb-2"><small class="text-muted">المراجع:</small> <strong>{{ $campaign->reviewer->name }}</strong> <span class="small text-muted">{{ $campaign->reviewed_at?->format('Y-m-d H:i') }}</span></div>
                @endif
                @if($campaign->approver)
                <div class="mb-2"><small class="text-muted">المعتمد:</small> <strong>{{ $campaign->approver->name }}</strong> <span class="small text-muted">{{ $campaign->approved_at?->format('Y-m-d H:i') }}</span></div>
                @endif
            </div>
        </div>

        <!-- History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-clock-history"></i> سجل الإجراءات</h6></div>
            <div class="card-body p-0">
                @forelse($history as $h)
                <div class="p-2 border-bottom small">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $h->action_label }}</strong>
                        <span class="text-muted">{{ $h->created_at->format('m-d H:i') }}</span>
                    </div>
                    <div class="text-muted">{{ $h->user->name ?? '' }}</div>
                    @if($h->comment) <div class="text-primary mt-1"><i class="bi bi-chat"></i> {{ $h->comment }}</div> @endif
                </div>
                @empty
                <div class="p-3 text-center text-muted small">لا توجد إجراءات سابقة</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
