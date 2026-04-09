@extends('layouts.admin')
@section('title', 'إدارة الأندية')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-building text-primary"></i> إدارة الأندية</h4>
    <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إضافة نادي</a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="league_id" class="form-select" onchange="this.form.submit()">
                    <option value="">جميع الدوريات</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}" {{ request('league_id') == $league->id ? 'selected' : '' }}>{{ $league->name_ar }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الشعار</th>
                        <th>الاسم بالعربية</th>
                        <th>الدوري</th>
                        <th>البريد</th>
                        <th>الجوال</th>
                        <th>اللاعبون</th>
                        <th>الإداريون</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clubs as $club)
                    <tr>
                        <td>{{ $club->id }}</td>
                        <td>
                            @if($club->logo)
                                <img src="{{ Storage::url($club->logo) }}" alt="" width="36" height="36" class="rounded-circle">
                            @else
                                <span class="badge bg-light text-dark"><i class="bi bi-image"></i></span>
                            @endif
                        </td>
                        <td>{{ $club->name_ar }}</td>
                        <td>
                            @if($club->league)
                                <a href="{{ route('admin.leagues.show', $club->league) }}" class="badge bg-primary text-decoration-none">{{ $club->league->name_ar }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $club->contact_email ?? '-' }}</td>
                        <td>{{ $club->contact_phone ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ $club->players_count }}</span></td>
                        <td><span class="badge bg-primary">{{ $club->admins_count }}</span></td>
                        <td>
                            @if($club->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-outline-primary" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.clubs.toggle-status', $club) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $club->is_active ? 'warning' : 'success' }}">
                                        <i class="bi bi-{{ $club->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">لا توجد أندية</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $clubs->withQueryString()->links() }}</div>
@endsection
