@extends('layouts.admin')
@section('title', 'موظفو الجمعية')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1"><i class="bi bi-person-workspace text-primary"></i> موظفو الجمعية</h4>
        <p class="text-muted mb-0 small">إدارة منشئي ومراجعي ومعتمدي حملات التصويت</p>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> إضافة موظف</a>
</div>

<!-- Roles Info -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 text-center small">
            <div class="col-md-4">
                <span class="badge bg-warning">منشئ التصويت</span><br>
                <small class="text-muted">ينشئ الحملات والأسئلة ويقدمها للمراجعة</small>
            </div>
            <div class="col-md-4">
                <span class="badge bg-info">مراجع التصويت</span><br>
                <small class="text-muted">يراجع الحملات ويعتمد أو يرفض</small>
            </div>
            <div class="col-md-4">
                <span class="badge bg-success">معتمد التصويت</span><br>
                <small class="text-muted">الاعتماد النهائي قبل الإرسال للأندية</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>الجوال</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $user)
                    @php
                        $role = $user->getRoleNames()->first();
                        $label = match($role) {
                            'campaign-creator' => ['منشئ التصويت', 'warning'],
                            'campaign-reviewer' => ['مراجع التصويت', 'info'],
                            'campaign-approver' => ['معتمد التصويت', 'success'],
                            default => [$role, 'secondary'],
                        };
                    @endphp
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td><span class="badge bg-{{ $label[1] }}">{{ $label[0] }}</span></td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.staff.edit', $user) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.staff.toggle-status', $user) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#pwdModal{{ $user->id }}">
                                    <i class="bi bi-key"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.staff.destroy', $user) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>

                            <div class="modal fade" id="pwdModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.staff.reset-password', $user) }}">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">إعادة تعيين كلمة مرور - {{ $user->name }}</h5>
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
                    <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد موظفون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $staff->links() }}</div>
@endsection
