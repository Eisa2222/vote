<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1c2e 0%, #2d3154 50%, #667eea 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .login-card { max-width: 420px; width: 100%; margin: auto; background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.3); overflow: hidden; }
        .login-header { background: linear-gradient(135deg, #1a1c2e, #2d3154); padding: 40px 30px; text-align: center; color: #fff; }
        .login-header h3 { margin: 0; font-size: 20px; }
        .login-body { padding: 35px 30px; }
        .form-control { border-radius: 10px; padding: 12px 16px; border: 2px solid #e9ecef; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.15); }
        .btn-login { background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px; border-radius: 10px; font-size: 16px; font-weight: 600; width: 100%; color: #fff; }
        .btn-login:hover { opacity: .9; color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-trophy fs-1 mb-2 d-block"></i>
            <h3>منصة التصويت الرياضي</h3>
            <p class="mb-0 opacity-75 small mt-1">تسجيل الدخول للوحة التحكم</p>
        </div>
        <div class="login-body">
            @if(session('status'))
                <div class="alert alert-success small">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger small">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger small">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">البريد الإلكتروني</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="example@email.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" required placeholder="********">
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">تذكرني</label>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول
                </button>
            </form>
        </div>
    </div>
</body>
</html>
