@extends('layouts.admin')
@section('title', 'نتائج الحملة')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1"><i class="bi bi-bar-chart text-primary"></i> نتائج الحملة</h4>
        <p class="text-muted mb-0">{{ $campaign->title }}</p>
    </div>
    <div>
        <a href="{{ route('admin.campaigns.export.csv', $campaign) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> تصدير CSV</a>
        <a href="{{ route('admin.campaigns.export.pdf', $campaign) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> تصدير PDF</a>
        <a href="{{ route('admin.campaigns.export.participation', $campaign) }}" class="btn btn-outline-info btn-sm"><i class="bi bi-download"></i> تقرير المشاركة</a>
    </div>
</div>

<!-- Overall Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h3 text-primary">{{ $stats['total_links'] }}</div>
                <div class="text-muted small">إجمالي الروابط</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h3 text-success">{{ $stats['total_voted'] }}</div>
                <div class="text-muted small">عدد المصوتين</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h3 text-danger">{{ $stats['total_not_voted'] }}</div>
                <div class="text-muted small">لم يصوتوا</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h3 text-info">{{ $stats['participation_rate'] }}%</div>
                <div class="text-muted small">نسبة المشاركة</div>
            </div>
        </div>
    </div>
</div>

<!-- Question Results -->
@foreach($campaign->questions as $question)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0">
            <span class="badge bg-primary me-2">{{ $question->sort_order }}</span>
            {{ $question->title }}
            <span class="badge bg-secondary ms-2">{{ $questionResults[$question->id]['total_responses'] }} إجابة</span>
        </h6>
    </div>
    <div class="card-body">
        @foreach($questionResults[$question->id]['results'] as $result)
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>{{ $result['label'] }}</span>
                <span class="fw-bold">{{ $result['count'] }} ({{ $result['percentage'] }}%)</span>
            </div>
            <div class="progress" style="height: 24px;">
                <div class="progress-bar bg-primary" style="width: {{ $result['percentage'] }}%">
                    {{ $result['percentage'] }}%
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

<!-- Club Stats -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-building text-success"></i> إحصائيات حسب النادي</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>النادي</th><th>الروابط</th><th>المصوتين</th><th>لم يصوتوا</th><th>نسبة المشاركة</th></tr>
                </thead>
                <tbody>
                    @foreach($clubStats as $cs)
                    <tr>
                        <td>{{ $cs['club']->name_ar ?? '' }}</td>
                        <td>{{ $cs['stats']['total_links'] }}</td>
                        <td><span class="text-success">{{ $cs['stats']['total_voted'] }}</span></td>
                        <td><span class="text-danger">{{ $cs['stats']['total_not_voted'] }}</span></td>
                        <td>
                            <div class="progress" style="height:20px;">
                                <div class="progress-bar bg-success" style="width: {{ $cs['stats']['participation_rate'] }}%">{{ $cs['stats']['participation_rate'] }}%</div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
