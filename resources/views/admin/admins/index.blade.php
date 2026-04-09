@extends('layouts.admin')
@section('title', 'إدارة الإداريين')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-people text-primary"></i> إدارة الإداريين</h4>
    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إضافة إداري</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الجوال</th>
                        <th>النادي</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->phone ?? '-' }}</td>
                        <td>{{ $admin->club->name_ar ?? '-' }}</td>
                        <td>
                            @if($admin->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.admins.toggle-status', $admin) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $admin->is_active ? 'warning' : 'success' }}">
                                        <i class="bi bi-{{ $admin->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#resetModal{{ $admin->id }}">
                                    <i class="bi bi-key"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>

                            <!-- Reset Password Modal -->
                            <div class="modal fade" id="resetModal{{ $admin->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.admins.reset-password', $admin) }}">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">إعادة تعيين كلمة المرور - {{ $admin->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">كلمة المرور الجديدة</label>
                                                <input type="password" name="password" class="form-control" required minlength="8">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">تحديث</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد إداريون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $admins->links() }}</div>
@endsection
