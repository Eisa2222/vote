<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مراجعة الإجابات - {{ $campaign->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .vote-container { max-width: 640px; margin: 0 auto; padding: 20px; }
        .vote-card { background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.15); overflow: hidden; }
        .vote-header { background: linear-gradient(135deg, #1a1c2e, #2d3154); color: #fff; padding: 30px; text-align: center; }
        .vote-body { padding: 25px; }
        .review-item { border: 1px solid #e9ecef; border-radius: 12px; padding: 16px; margin-bottom: 12px; }
        .btn-submit { background: linear-gradient(135deg, #28a745, #218838); border: none; padding: 14px; font-size: 18px; border-radius: 12px; width: 100%; color: #fff; font-weight: 600; }
        .btn-back { background: #6c757d; border: none; padding: 14px; font-size: 16px; border-radius: 12px; width: 100%; color: #fff; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="vote-container">
        <div class="vote-card">
            <div class="vote-header">
                <i class="bi bi-eye fs-1"></i>
                <h2>مراجعة الإجابات</h2>
                <p class="mb-0 opacity-75">{{ $campaign->title }}</p>
            </div>
            <div class="vote-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> يرجى مراجعة إجاباتك قبل الإرسال النهائي. لا يمكن التعديل بعد الإرسال.
                </div>

                @foreach($reviewData as $item)
                <div class="review-item">
                    <div class="fw-bold mb-1">{{ $item['question']->sort_order }}. {{ $item['question']->title }}</div>
                    <div class="text-primary">
                        <i class="bi bi-check-circle"></i>
                        {{ $item['display'] ?: 'لم تتم الإجابة' }}
                    </div>
                </div>
                @endforeach

                <form method="POST" action="{{ route('vote.submit', $link->token) }}">
                    @csrf
                    @foreach($answers as $qId => $answer)
                        @if(is_array($answer))
                            @foreach($answer as $val)
                                <input type="hidden" name="answers[{{ $qId }}][]" value="{{ $val }}">
                            @endforeach
                        @else
                            <input type="hidden" name="answers[{{ $qId }}]" value="{{ $answer }}">
                        @endif
                    @endforeach
                    <button type="submit" class="btn btn-submit" onclick="return confirm('هل أنت متأكد من إرسال التصويت؟ لا يمكن التعديل بعد الإرسال.')">
                        <i class="bi bi-check-circle"></i> تأكيد وإرسال التصويت
                    </button>
                </form>

                <button onclick="history.back()" class="btn btn-back">
                    <i class="bi bi-arrow-right"></i> العودة للتعديل
                </button>
            </div>
        </div>
    </div>
</body>
</html>
