@extends('layouts.admin')
@section('title', 'لوحة تحكم النادي')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-speedometer2 text-primary"></i> لوحة تحكم النادي - {{ auth()->user()->club->name_ar ?? '' }}</h4>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="bi bi-person-badge"></i></div>
                <div>
                    <div class="text-muted small">اللاعبون</div>
                    <div class="h4 mb-0">{{ $stats['players_count'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">صوّتوا</div>
                    <div class="h4 mb-0">{{ $stats['total_voted'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3"><i class="bi bi-link-45deg"></i></div>
                <div>
                    <div class="text-muted small">الروابط المرسلة</div>
                    <div class="h4 mb-0">{{ $stats['links_sent'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3"><i class="bi bi-graph-up"></i></div>
                <div>
                    <div class="text-muted small">نسبة المشاركة</div>
                    <div class="h4 mb-0">{{ $stats['participation_rate'] }}%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-megaphone text-primary"></i> آخر الحملات الواردة</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>الحملة</th><th>الحالة</th><th>التاريخ</th><th>الإجراءات</th></tr></thead>
                <tbody>
                    @forelse($recentAssignments as $assignment)
                    <tr>
                        <td>{{ $assignment->campaign->title ?? '' }}</td>
                        <td>
                            @switch($assignment->campaign->status ?? '')
                                @case('active') <span class="badge bg-success">نشطة</span> @break
                                @case('closed') <span class="badge bg-danger">مغلقة</span> @break
                                @default <span class="badge bg-secondary">{{ $assignment->campaign->status ?? '' }}</span>
                            @endswitch
                        </td>
                        <td class="small text-muted">{{ $assignment->sent_at?->format('Y-m-d') ?? '' }}</td>
                        <td>
                            <a href="{{ route('club.campaigns.show', $assignment->campaign_id) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">لا توجد حملات واردة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
