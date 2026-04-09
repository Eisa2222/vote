<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم التصويت مسبقاً</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .info-card { max-width: 500px; margin: auto; background: #fff; border-radius: 20px; padding: 50px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
        .info-icon { width: 100px; height: 100px; background: #fff3cd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .info-icon i { font-size: 50px; color: #ffc107; }
    </style>
</head>
<body>
    <div class="info-card">
        <div class="info-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h2 class="text-warning mb-3">تم تسجيل تصويتك مسبقاً</h2>
        <p class="text-muted mb-4">عذراً {{ $player->name }}، لقد قمت بالتصويت مسبقاً ولا يمكن التصويت مرة أخرى.</p>
        <p class="text-muted small">إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع إداري ناديك.</p>
    </div>
</body>
</html>
