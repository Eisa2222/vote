@extends('layouts.admin')
@section('title', 'تفاصيل الحملة')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1"><i class="bi bi-megaphone text-primary"></i> {{ $campaign->title }}</h4>
        <p class="text-muted mb-0">{{ $campaign->description }}</p>
    </div>
    <div>
        <form method="POST" action="{{ route('club.campaigns.generate-links', $campaign) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="bi bi-link-45deg"></i> توليد روابط التصويت</button>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h3 text-primary">{{ $stats['total_links'] }}</div>
                <div class="text-muted small">الروابط</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="h3 text-success">{{ $stats['total_voted'] }}</div>
                <div class="text-muted small">صوّتوا</div>
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

@if($campaign->show_countdown && $campaign->ends_at && $campaign->ends_at->isFuture())
<div class="alert alert-info text-center">
    <i class="bi bi-clock"></i> ينتهي التصويت في: <strong>{{ $campaign->ends_at->format('Y-m-d H:i') }}</strong>
    <span id="countdown"></span>
</div>
@endif

<!-- Questions Preview -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white"><h6 class="mb-0">الأسئلة ({{ $campaign->questions->count() }})</h6></div>
    <div class="card-body">
        @foreach($campaign->questions as $question)
        <div class="mb-3 p-3 bg-light rounded">
            <strong>{{ $question->sort_order }}. {{ $question->title }}</strong>
            @if($question->is_required) <span class="badge bg-danger">إجباري</span> @endif
            <span class="badge bg-{{ $question->type == 'radio' ? 'info' : 'warning' }}">{{ $question->type == 'radio' ? 'اختيار واحد' : 'اختيار متعدد' }}</span>
            <div class="mt-2">
                @foreach($question->options as $opt)
                    <span class="badge bg-white text-dark border me-1">{{ $opt->label }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Voting Links -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">روابط التصويت</h6>
        @if($links->where('status', 'pending')->count() > 0)
        <form method="POST" action="{{ route('club.campaigns.send-links', $campaign) }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-send"></i> تحديث حالة الكل لـ "مُرسل"</button>
        </form>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>اللاعب</th><th>الرابط</th><th>الحالة</th><th>تاريخ الإرسال</th><th>تاريخ التصويت</th></tr></thead>
                <tbody>
                    @forelse($links as $link)
                    <tr>
                        <td>{{ $link->player->name ?? '' }}</td>
                        <td>
                            <div class="input-group input-group-sm" style="max-width:400px;">
                                <input type="text" class="form-control form-control-sm" value="{{ route('vote.show', $link->token) }}" readonly id="link-{{ $link->id }}">
                                <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText(document.getElementById('link-{{ $link->id }}').value);this.innerHTML='<i class=\'bi bi-check\'></i>'"><i class="bi bi-clipboard"></i></button>
                            </div>
                        </td>
                        <td>
                            @switch($link->status)
                                @case('pending') <span class="badge bg-warning">في الانتظار</span> @break
                                @case('sent') <span class="badge bg-info">تم الإرسال</span> @break
                                @case('used') <span class="badge bg-success">تم التصويت</span> @break
                                @case('expired') <span class="badge bg-secondary">منتهي</span> @break
                            @endswitch
                        </td>
                        <td class="small">{{ $link->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="small">{{ $link->used_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">لم يتم توليد روابط بعد. اضغط "توليد روابط التصويت"</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $links->links() }}</div>
@endsection
