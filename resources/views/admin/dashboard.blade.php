@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-speedometer2 text-primary"></i> لوحة تحكم الجمعية</h4>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-dark bg-opacity-10 text-dark me-3">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <div class="text-muted small">الدوريات</div>
                    <div class="h4 mb-0">{{ $stats['leagues_count'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <div class="text-muted small">الأندية</div>
                    <div class="h4 mb-0">{{ $stats['clubs_count'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="text-muted small">الإداريون</div>
                    <div class="h4 mb-0">{{ $stats['admins_count'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-person-badge"></i>
                </div>
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
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-megaphone"></i>
                </div>
                <div>
                    <div class="text-muted small">الحملات النشطة</div>
                    <div class="h4 mb-0">{{ $stats['active_campaigns'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h2 text-primary mb-1">{{ $stats['total_votes'] }}</div>
                <div class="text-muted">إجمالي الأصوات</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h2 text-success mb-1">{{ $stats['participation_rate'] }}%</div>
                <div class="text-muted">نسبة المشاركة</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h2 text-info mb-1">{{ $stats['total_links'] }}</div>
                <div class="text-muted">روابط التصويت</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="bi bi-megaphone text-primary"></i> آخر الحملات</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>الحملة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                        <tbody>
                            @forelse($recentCampaigns as $campaign)
                            <tr>
                                <td>{{ $campaign->title }}</td>
                                <td>
                                    @switch($campaign->status)
                                        @case('draft') <span class="badge bg-secondary">مسودة</span> @break
                                        @case('active') <span class="badge bg-success">نشطة</span> @break
                                        @case('closed') <span class="badge bg-danger">مغلقة</span> @break
                                        @case('archived') <span class="badge bg-dark">مؤرشفة</span> @break
                                        @case('scheduled') <span class="badge bg-info">مجدولة</span> @break
                                    @endswitch
                                </td>
                                <td class="small text-muted">{{ $campaign->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">لا توجد حملات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="bi bi-building text-success"></i> إحصائيات الأندية</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>النادي</th><th>اللاعبون</th><th>الروابط</th></tr></thead>
                        <tbody>
                            @foreach($clubStats as $club)
                            <tr>
                                <td>{{ $club->name_ar }}</td>
                                <td>{{ $club->players_count }}</td>
                                <td>{{ $club->voting_links_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
