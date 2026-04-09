@extends('layouts.admin')
@section('title', 'نتائج الحملة')

@section('content')
<div class="page-header">
    <h4 class="mb-1"><i class="bi bi-bar-chart text-primary"></i> نتائج الحملة - {{ $campaign->title }}</h4>
    <p class="text-muted mb-0">إحصائيات ناديك فقط</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card"><div class="card-body text-center">
            <div class="h3 text-primary">{{ $stats['total_links'] }}</div><div class="text-muted small">الروابط</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card"><div class="card-body text-center">
            <div class="h3 text-success">{{ $stats['total_voted'] }}</div><div class="text-muted small">صوّتوا</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card"><div class="card-body text-center">
            <div class="h3 text-danger">{{ $stats['total_not_voted'] }}</div><div class="text-muted small">لم يصوتوا</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card"><div class="card-body text-center">
            <div class="h3 text-info">{{ $stats['participation_rate'] }}%</div><div class="text-muted small">نسبة المشاركة</div>
        </div></div>
    </div>
</div>

@foreach($campaign->questions as $question)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0">{{ $question->sort_order }}. {{ $question->title }}</h6>
    </div>
    <div class="card-body">
        @foreach($questionResults[$question->id]['results'] as $result)
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>{{ $result['label'] }}</span>
                <span class="fw-bold">{{ $result['count'] }} ({{ $result['percentage'] }}%)</span>
            </div>
            <div class="progress" style="height: 24px;">
                <div class="progress-bar bg-primary" style="width: {{ $result['percentage'] }}%">{{ $result['percentage'] }}%</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach
@endsection
