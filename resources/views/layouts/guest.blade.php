<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'منصة التصويت الرياضي') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1c2e 0%, #2d3154 50%, #667eea 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; padding: 20px 0; }
        .auth-card { max-width: 460px; width: 100%; margin: auto; background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.3); overflow: hidden; }
        .auth-header { background: linear-gradient(135deg, #1a1c2e, #2d3154); padding: 30px; text-align: center; color: #fff; }
        .auth-header h3 { margin: 0; font-size: 18px; }
        .auth-body { padding: 30px; }
        .form-control { border-radius: 10px; padding: 12px 16px; border: 2px solid #e9ecef; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.15); }
        .btn-auth { background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px; border-radius: 10px; font-size: 16px; font-weight: 600; width: 100%; color: #fff; }
        .btn-auth:hover { opacity: .9; color: #fff; }
        .auth-link { color: #667eea; text-decoration: none; font-weight: 500; }
        .auth-link:hover { color: #764ba2; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-trophy fs-1 mb-2 d-block"></i>
            <h3>@yield('heading', 'منصة التصويت الرياضي')</h3>
            @hasSection('subheading')
                <p class="mb-0 opacity-75 small mt-1">@yield('subheading')</p>
            @endif
        </div>
        <div class="auth-body">
            @if(session('status'))
                <div class="alert alert-success small"><i class="bi bi-check-circle"></i> {{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger small"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger small">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
