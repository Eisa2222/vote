@extends('layouts.admin')
@section('title', 'تتبع الحملة')

@section('content')
<div class="page-header">
    <h4 class="mb-1"><i class="bi bi-eye text-primary"></i> تتبع الحملة</h4>
    <p class="text-muted mb-0">{{ $campaign->title }}</p>
</div>

<!-- Assignments -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white"><h6 class="mb-0">حالة الإرسال للإداريين</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>الإداري</th><th>النادي</th><th>الحالة</th><th>تاريخ الإرسال</th><th>تاريخ الفتح</th></tr></thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->admin->name ?? '' }}</td>
                        <td>{{ $assignment->club->name_ar ?? '' }}</td>
                        <td>
                            @switch($assignment->status)
                                @case('sent') <span class="badge bg-success">تم الإرسال</span> @break
                                @case('delivered') <span class="badge bg-info">تم التوصيل</span> @break
                                @case('pending') <span class="badge bg-warning">في الانتظار</span> @break
                                @case('failed') <span class="badge bg-danger">فشل</span> @break
                            @endswitch
                        </td>
                        <td>{{ $assignment->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $assignment->opened_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Voting Links -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0">روابط التصويت</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>اللاعب</th><th>النادي</th><th>الحالة</th><th>تاريخ الإرسال</th><th>تاريخ التصويت</th><th>صلاحية الرابط</th></tr></thead>
                <tbody>
                    @forelse($links as $link)
                    <tr>
                        <td>{{ $link->player->name ?? '' }}</td>
                        <td>{{ $link->club->name_ar ?? '' }}</td>
                        <td>
                            @switch($link->status)
                                @case('pending') <span class="badge bg-warning">في الانتظار</span> @break
                                @case('sent') <span class="badge bg-info">تم الإرسال</span> @break
                                @case('used') <span class="badge bg-success">تم التصويت</span> @break
                                @case('expired') <span class="badge bg-secondary">منتهي</span> @break
                            @endswitch
                        </td>
                        <td>{{ $link->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $link->used_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $link->expires_at?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">لم يتم توليد روابط بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $links->links() }}</div>
@endsection
