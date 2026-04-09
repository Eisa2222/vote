@extends('layouts.admin')
@section('title', 'بيانات اللاعب')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person text-primary"></i> {{ $player->name }}</h4>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                @if($player->photo)
                    <img src="{{ Storage::url($player->photo) }}" alt="" class="rounded-circle mb-3" width="100">
                @else
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;">
                        <i class="bi bi-person-fill fs-1 text-muted"></i>
                    </div>
                @endif
                <h5>{{ $player->name }}</h5>
                <p class="text-muted">{{ $player->club->name_ar ?? '' }}</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span>رقم اللاعب</span><strong>{{ $player->player_number ?? '-' }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>المركز</span><strong>{{ $player->position ?? '-' }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>الجنسية</span><strong>{{ $player->nationality ?? '-' }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>الجوال</span><strong>{{ $player->phone ?? '-' }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>الحالة</span>
                    @switch($player->status)
                        @case('active') <span class="badge bg-success">نشط</span> @break
                        @case('suspended') <span class="badge bg-warning">موقوف</span> @break
                        @case('injured') <span class="badge bg-danger">مصاب</span> @break
                        @case('retired') <span class="badge bg-secondary">معتزل</span> @break
                        @default <span class="badge bg-dark">غير مفعل</span>
                    @endswitch
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">سجل التصويت</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>الحملة</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                        <tbody>
                            @forelse($player->votingLinks as $link)
                            <tr>
                                <td>{{ $link->campaign->title ?? '' }}</td>
                                <td>
                                    @switch($link->status)
                                        @case('used') <span class="badge bg-success">صوّت</span> @break
                                        @case('sent') <span class="badge bg-info">تم الإرسال</span> @break
                                        @case('pending') <span class="badge bg-warning">في الانتظار</span> @break
                                        @case('expired') <span class="badge bg-secondary">منتهي</span> @break
                                    @endswitch
                                </td>
                                <td>{{ $link->used_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">لا يوجد سجل تصويت</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
