@extends('layouts.admin')
@section('title', 'حملات التصويت')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-megaphone text-primary"></i> حملات التصويت</h4>
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إنشاء حملة</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>العنوان</th><th>سير العمل</th><th>الحالة</th><th>الأسئلة</th><th>الروابط</th><th>الأصوات</th><th>البداية</th><th>النهاية</th><th>الإجراءات</th></tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->id }}</td>
                        <td>{{ $campaign->title }}</td>
                        <td>
                            <span class="badge bg-{{ $campaign->workflow_status_color }}">{{ $campaign->workflow_status_label }}</span>
                        </td>
                        <td>
                            @switch($campaign->status)
                                @case('draft') <span class="badge bg-secondary">مسودة</span> @break
                                @case('scheduled') <span class="badge bg-info">مجدولة</span> @break
                                @case('active') <span class="badge bg-success">نشطة</span> @break
                                @case('closed') <span class="badge bg-danger">مغلقة</span> @break
                                @case('archived') <span class="badge bg-dark">مؤرشفة</span> @break
                            @endswitch
                        </td>
                        <td><span class="badge bg-light text-dark">{{ $campaign->questions_count }}</span></td>
                        <td><span class="badge bg-light text-dark">{{ $campaign->voting_links_count }}</span></td>
                        <td><span class="badge bg-primary">{{ $campaign->responses_count }}</span></td>
                        <td class="small">{{ $campaign->starts_at?->format('Y-m-d') ?? '-' }}</td>
                        <td class="small">{{ $campaign->ends_at?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.campaigns.questions', $campaign) }}" class="btn btn-outline-info" title="الأسئلة"><i class="bi bi-list-check"></i></a>
                                <a href="{{ route('admin.campaigns.send', $campaign) }}" class="btn btn-outline-success" title="إرسال"><i class="bi bi-send"></i></a>
                                <a href="{{ route('admin.campaigns.results', $campaign) }}" class="btn btn-outline-primary" title="النتائج"><i class="bi bi-bar-chart"></i></a>
                                <a href="{{ route('admin.campaigns.tracking', $campaign) }}" class="btn btn-outline-secondary" title="التتبع"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-outline-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">لا توجد حملات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $campaigns->links() }}</div>
@endsection
