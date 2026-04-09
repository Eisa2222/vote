@extends('layouts.admin')
@section('title', 'أندية الدوري')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1">
            @if($league->logo)
                <img src="{{ Storage::url($league->logo) }}" alt="" width="32" class="rounded-circle me-2">
            @endif
            <i class="bi bi-trophy text-primary"></i> {{ $league->name_ar }}
        </h4>
        <p class="text-muted mb-0">
            @if($league->season) الموسم: {{ $league->season }} | @endif
            عدد الأندية: {{ $league->clubs->count() }}
        </p>
    </div>
    <div>
        <a href="{{ route('admin.clubs.create', ['league_id' => $league->id]) }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إضافة نادي لهذا الدوري</a>
        <a href="{{ route('admin.leagues.index') }}" class="btn btn-outline-secondary">رجوع</a>
    </div>
</div>

@if($league->description)
<div class="alert alert-light border mb-3">{{ $league->description }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الشعار</th>
                        <th>النادي</th>
                        <th>البريد</th>
                        <th>الجوال</th>
                        <th>اللاعبون</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($league->clubs as $club)
                    <tr>
                        <td>{{ $club->id }}</td>
                        <td>
                            @if($club->logo)
                                <img src="{{ Storage::url($club->logo) }}" alt="" width="32" height="32" class="rounded-circle">
                            @else
                                <span class="badge bg-light text-dark"><i class="bi bi-image"></i></span>
                            @endif
                        </td>
                        <td>{{ $club->name_ar }}</td>
                        <td>{{ $club->contact_email ?? '-' }}</td>
                        <td>{{ $club->contact_phone ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ $club->players_count }}</span></td>
                        <td>
                            @if($club->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">لا توجد أندية في هذا الدوري بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
