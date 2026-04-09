@extends('layouts.admin')
@section('title', 'لاعبو النادي')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-person-badge text-primary"></i> لاعبو النادي</h4>
    <div>
        <a href="{{ route('club.players.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إضافة لاعب</a>
        <a href="{{ route('club.players.import-form') }}" class="btn btn-outline-success"><i class="bi bi-upload"></i> استيراد</a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو الهوية أو الجوال" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
                    <option value="injured" {{ request('status') == 'injured' ? 'selected' : '' }}>مصاب</option>
                    <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>معتزل</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير مفعل</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> بحث</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>الاسم</th><th>رقم اللاعب</th><th>الجوال</th><th>المركز</th><th>الجنسية</th><th>الحالة</th><th>الإجراءات</th></tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                    <tr>
                        <td>{{ $player->id }}</td>
                        <td>{{ $player->name }}</td>
                        <td>{{ $player->player_number ?? '-' }}</td>
                        <td>{{ $player->phone ?? '-' }}</td>
                        <td>{{ $player->position ?? '-' }}</td>
                        <td>{{ $player->nationality ?? '-' }}</td>
                        <td>
                            @switch($player->status)
                                @case('active') <span class="badge bg-success">نشط</span> @break
                                @case('suspended') <span class="badge bg-warning">موقوف</span> @break
                                @case('injured') <span class="badge bg-danger">مصاب</span> @break
                                @case('retired') <span class="badge bg-secondary">معتزل</span> @break
                                @default <span class="badge bg-dark">غير مفعل</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('club.players.edit', $player) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('club.players.destroy', $player) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">لا يوجد لاعبون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $players->withQueryString()->links() }}</div>
@endsection
