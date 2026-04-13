@extends('layouts.admin')
@section('title', 'الملف الشخصي')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-person-gear text-primary"></i> الملف الشخصي</h4>
</div>

@if(session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> تم تحديث البيانات بنجاح
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> تم تحديث كلمة المرور بنجاح
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">
    <!-- Profile Information -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-person-circle text-primary"></i> بيانات الحساب</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle"></i> بريدك الإلكتروني غير مؤكد.
                        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 small">إعادة إرسال رابط التأكيد</button>
                        </form>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ التغييرات</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Password -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-key text-warning"></i> تغيير كلمة المرور</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">كلمة المرور الحالية</label>
                        <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                        @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-shield-lock"></i> تحديث كلمة المرور</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="col-12">
        <div class="card border-danger shadow-sm">
            <div class="card-header bg-danger bg-opacity-10">
                <h6 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle"></i> منطقة الخطر</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">بمجرد حذف حسابك، ستُحذف جميع بياناتك ومواردك بشكل دائم. يُرجى تنزيل أي بيانات ترغب في الاحتفاظ بها قبل الحذف.</p>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash"></i> حذف الحساب
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> تأكيد حذف الحساب</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف حسابك؟ بمجرد الحذف، ستُحذف جميع البيانات بشكل دائم.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">أدخل كلمة المرور للتأكيد</label>
                        <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" required>
                        @error('password', 'userDeletion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> حذف الحساب نهائياً</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
});
</script>
@endpush
@endif
@endsection
