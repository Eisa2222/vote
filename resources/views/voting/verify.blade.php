<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التحقق من الهوية - {{ $campaign->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .verify-card { max-width: 500px; margin: auto; background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.15); overflow: hidden; }
        .verify-header { background: linear-gradient(135deg, #1a1c2e, #2d3154); color: #fff; padding: 30px; text-align: center; }
        .verify-body { padding: 30px; }
        .btn-verify { background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 14px; font-size: 16px; border-radius: 12px; width: 100%; color: #fff; font-weight: 600; }
        .btn-verify:hover { opacity: .9; color: #fff; }
        .form-control { border-radius: 10px; padding: 14px 16px; border: 2px solid #e9ecef; font-size: 16px; text-align: center; letter-spacing: 1px; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.15); }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-header">
            <i class="bi bi-shield-lock fs-1 d-block mb-2"></i>
            <h4 class="mb-1">التحقق من الهوية</h4>
            <p class="mb-0 opacity-75 small">{{ $campaign->title }}</p>
            <div class="mt-2 opacity-50 small">{{ $club->name_ar }}</div>
        </div>
        <div class="verify-body">
            @if($errors->any())
                <div class="alert alert-danger small">
                    <i class="bi bi-exclamation-triangle"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="alert alert-info small">
                <i class="bi bi-info-circle"></i>
                للتحقق من هويتك، يرجى إدخال <strong>رقم الهوية</strong> أو <strong>رقم الجوال</strong> المسجل لدى النادي.
            </div>

            <form method="POST" action="{{ ($mode ?? 'individual') === 'shared' ? route('vote.shared.verify', $token) : route('vote.verify', $token) }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold text-center d-block">رقم الهوية أو رقم الجوال</label>
                    <input type="text" name="national_id" class="form-control" placeholder="أدخل رقم الهوية أو الجوال" required autofocus>
                </div>
                <button type="submit" class="btn btn-verify">
                    <i class="bi bi-check-circle"></i> تحقق ومتابعة التصويت
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted"><i class="bi bi-lock"></i> بياناتك محمية ولن تُستخدم لغير التحقق</small>
            </div>
        </div>
    </div>
</body>
</html>
