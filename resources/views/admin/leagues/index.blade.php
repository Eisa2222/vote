@extends('layouts.admin')
@section('title', 'إدارة الدوريات')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-trophy text-primary"></i> إدارة الدوريات</h4>
    <a href="{{ route('admin.leagues.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إضافة دوري</a>
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
                        <th>الاسم بالإنجليزية</th>
                        <th>الموسم</th>
                        <th>عدد الأندية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leagues as $league)
                    <tr>
                        <td>{{ $league->id }}</td>
                        <td>
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" alt="" width="36" height="36" class="rounded-circle">
                            @else
                                <span class="badge bg-light text-dark"><i class="bi bi-trophy"></i></span>
                            @endif
                        </td>
                        <td><a href="{{ route('admin.leagues.show', $league) }}" class="text-decoration-none fw-bold">{{ $league->name_ar }}</a></td>
                        <td>{{ $league->name_en ?? '-' }}</td>
                        <td>{{ $league->season ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ $league->clubs_count }}</span></td>
                        <td>
                            @if($league->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.leagues.show', $league) }}" class="btn btn-outline-info" title="عرض الأندية"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.leagues.edit', $league) }}" class="btn btn-outline-primary" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.leagues.toggle-status', $league) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $league->is_active ? 'warning' : 'success' }}" title="{{ $league->is_active ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="bi bi-{{ $league->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.leagues.destroy', $league) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">لا توجد دوريات. أنشئ دوري أولاً ثم أضف الأندية.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $leagues->links() }}</div>
@endsection
