<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم التصويت بنجاح</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #28a745 0%, #218838 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .success-card { max-width: 500px; margin: auto; background: #fff; border-radius: 20px; padding: 50px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
        .success-icon { width: 100px; height: 100px; background: #d4edda; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .success-icon i { font-size: 50px; color: #28a745; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2 class="text-success mb-3">تم تسجيل تصويتك بنجاح!</h2>
        <p class="text-muted mb-4">شكراً لك {{ $player->name }} على مشاركتك في "{{ $campaign->title }}".</p>
        <p class="text-muted small">يمكنك إغلاق هذه الصفحة الآن.</p>
    </div>
</body>
</html>
