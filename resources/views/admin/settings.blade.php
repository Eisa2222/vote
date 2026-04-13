@extends('layouts.admin')
@section('title', 'إعدادات النظام')

@section('content')
<div class="page-header">
    <h4 class="mb-0"><i class="bi bi-gear text-primary"></i> إعدادات النظام</h4>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-info-circle text-primary"></i> معلومات النظام</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="180">اسم المنصة</td><td>{{ config('app.name') }}</td></tr>
                    <tr><td class="text-muted">الإصدار</td><td>1.0.0</td></tr>
                    <tr><td class="text-muted">بيئة العمل</td><td>{{ config('app.env') }}</td></tr>
                    <tr><td class="text-muted">اللغة</td><td>{{ config('app.locale') }}</td></tr>
                    <tr><td class="text-muted">PHP</td><td>{{ phpversion() }}</td></tr>
                    <tr><td class="text-muted">Laravel</td><td>{{ app()->version() }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-bar-chart text-success"></i> إحصائيات سريعة</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="180">الدوريات</td><td>{{ \App\Models\League::count() }}</td></tr>
                    <tr><td class="text-muted">الأندية</td><td>{{ \App\Models\Club::count() }}</td></tr>
                    <tr><td class="text-muted">الإداريون</td><td>{{ \App\Models\User::role('club-admin')->count() }}</td></tr>
                    <tr><td class="text-muted">اللاعبون</td><td>{{ \App\Models\Player::count() }}</td></tr>
                    <tr><td class="text-muted">حملات التصويت</td><td>{{ \App\Models\VotingCampaign::count() }}</td></tr>
                    <tr><td class="text-muted">إجمالي الأصوات</td><td>{{ \App\Models\VotingResponse::count() }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-envelope text-info"></i> إعدادات البريد</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="180">مزود البريد</td><td>{{ config('mail.default') }}</td></tr>
                    <tr><td class="text-muted">البريد المرسل</td><td>{{ config('mail.from.address') }}</td></tr>
                    <tr><td class="text-muted">اسم المرسل</td><td>{{ config('mail.from.name') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-phone text-success"></i> بوابة SMS</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="180">المزود</td><td>
                        @php $smsProvider = config('sms.provider', 'log'); @endphp
                        @if($smsProvider === 'log')
                            <span class="badge bg-warning">وضع التطوير (Log)</span>
                        @else
                            <span class="badge bg-success">{{ strtoupper($smsProvider) }}</span>
                        @endif
                    </td></tr>
                    <tr><td class="text-muted">اسم المرسل</td><td>{{ config('sms.sender_name') }}</td></tr>
                    <tr><td class="text-muted">الحالة</td><td>
                        @if($smsProvider !== 'log')
                            <span class="badge bg-success">مفعّل</span>
                        @else
                            <span class="badge bg-secondary">تطوير</span>
                        @endif
                    </td></tr>
                </table>
                <div class="alert alert-light small mt-2 mb-0">
                    <strong>البوابات المدعومة:</strong> Taqnyat, Unifonic, Msegat<br>
                    <strong>للتفعيل:</strong> عدّل <code>SMS_PROVIDER</code> و مفاتيح API في ملف <code>.env</code>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-shield-lock text-danger"></i> الأمان</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="180">وضع التصحيح</td><td>
                        @if(config('app.debug'))
                            <span class="badge bg-danger">مفعل - غير آمن</span>
                        @else
                            <span class="badge bg-success">معطل - آمن</span>
                        @endif
                    </td></tr>
                    <tr><td class="text-muted">التسجيل العام</td><td><span class="badge bg-success">مغلق</span></td></tr>
                    <tr><td class="text-muted">CSRF</td><td><span class="badge bg-success">مفعل</span></td></tr>
                    <tr><td class="text-muted">التحقق من الهوية</td><td><span class="badge bg-success">مفعل</span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
