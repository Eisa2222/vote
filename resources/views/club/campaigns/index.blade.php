@extends('layouts.admin')
@section('title', 'حملات التصويت')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-megaphone text-primary"></i> حملات التصويت الواردة</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>الحملة</th><th>الأسئلة</th><th>الحالة</th><th>النهاية</th><th>تاريخ الاستلام</th><th>الإجراءات</th></tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    @php $campaign = $assignment->campaign; @endphp
                    <tr>
                        <td>{{ $campaign->id ?? '' }}</td>
                        <td>{{ $campaign->title ?? '' }}</td>
                        <td><span class="badge bg-info">{{ $campaign->questions_count ?? 0 }}</span></td>
                        <td>
                            @switch($campaign->status ?? '')
                                @case('active') <span class="badge bg-success">نشطة</span> @break
                                @case('closed') <span class="badge bg-danger">مغلقة</span> @break
                                @default <span class="badge bg-secondary">{{ $campaign->status ?? '' }}</span>
                            @endswitch
                        </td>
                        <td class="small">{{ $campaign->ends_at?->format('Y-m-d') ?? '-' }}</td>
                        <td class="small">{{ $assignment->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('club.campaigns.show', $campaign->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> عرض</a>
                            <a href="{{ route('club.campaigns.results', $campaign->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-bar-chart"></i> النتائج</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">لا توجد حملات واردة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $assignments->links() }}</div>
@endsection
