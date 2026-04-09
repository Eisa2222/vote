<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $campaign->title }} - تصويت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .vote-container { max-width: 640px; margin: 0 auto; padding: 20px; }
        .vote-card { background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.15); overflow: hidden; }
        .vote-header { background: linear-gradient(135deg, #1a1c2e, #2d3154); color: #fff; padding: 30px; text-align: center; }
        .vote-header h2 { font-size: 20px; margin-bottom: 8px; }
        .vote-body { padding: 25px; }
        .question-card { border: 1px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 16px; transition: all .2s; }
        .question-card:hover { border-color: #667eea; }
        .question-title { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .option-label { display: block; padding: 10px 16px; border: 2px solid #e9ecef; border-radius: 10px; margin-bottom: 8px; cursor: pointer; transition: all .2s; }
        .option-label:hover { border-color: #667eea; background: #f8f9ff; }
        .option-label input { margin-left: 10px; }
        .form-check-input:checked + .option-text { color: #667eea; font-weight: 600; }
        .option-label:has(input:checked) { border-color: #667eea; background: #f0f0ff; }
        .btn-vote { background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 14px; font-size: 18px; border-radius: 12px; width: 100%; color: #fff; font-weight: 600; }
        .btn-vote:hover { opacity: .9; color: #fff; }
        .countdown { background: rgba(255,255,255,.15); padding: 8px 16px; border-radius: 8px; display: inline-block; margin-top: 10px; }
        .required-star { color: #dc3545; }
    </style>
</head>
<body>
    <div class="vote-container">
        <div class="vote-card">
            <div class="vote-header">
                <i class="bi bi-trophy fs-1"></i>
                <h2>{{ $campaign->title }}</h2>
                @if($campaign->description)
                    <p class="mb-0 opacity-75 small">{{ $campaign->description }}</p>
                @endif
                @if($campaign->show_countdown && $campaign->ends_at)
                    <div class="countdown" id="countdown">
                        <i class="bi bi-clock"></i> <span id="timer"></span>
                    </div>
                @endif
                <div class="mt-2 small opacity-75">
                    <i class="bi bi-person"></i> {{ $link->player->name }}
                    | <i class="bi bi-building"></i> {{ $link->club->name_ar }}
                </div>
            </div>
            <div class="vote-body">
                <form method="POST" action="{{ $campaign->allow_review_before_submit ? route('vote.review', $link->token) : route('vote.submit', $link->token) }}" id="voteForm">
                    @csrf

                    @foreach($questions as $index => $question)
                    <div class="question-card">
                        <div class="question-title">
                            {{ $index + 1 }}. {{ $question->title }}
                            @if($question->is_required) <span class="required-star">*</span> @endif
                        </div>
                        @if($question->description)
                            <small class="text-muted">{{ $question->description }}</small>
                        @endif
                        <div class="mt-3">
                            @if($question->type === 'radio')
                                @foreach($question->options as $option)
                                <label class="option-label">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="form-check-input"
                                        {{ $question->is_required ? 'required' : '' }}>
                                    <span class="option-text">{{ $option->label }}</span>
                                </label>
                                @endforeach
                            @elseif($question->type === 'checkbox')
                                @foreach($question->options as $option)
                                <label class="option-label">
                                    <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" class="form-check-input checkbox-{{ $question->id }}"
                                        data-max="{{ $question->max_selections }}">
                                    <span class="option-text">{{ $option->label }}</span>
                                </label>
                                @endforeach
                                @if($question->max_selections)
                                    <small class="text-muted">الحد الأقصى: {{ $question->max_selections }} اختيارات</small>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-vote mt-3">
                        <i class="bi bi-{{ $campaign->allow_review_before_submit ? 'eye' : 'check-circle' }}"></i>
                        {{ $campaign->allow_review_before_submit ? 'مراجعة الإجابات' : 'إرسال التصويت' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Checkbox max selections
    document.querySelectorAll('[data-max]').forEach(function(cb) {
        cb.addEventListener('change', function() {
            const max = parseInt(this.dataset.max);
            if (!max) return;
            const group = document.querySelectorAll('.checkbox-' + this.name.match(/\d+/)[0]);
            const checked = Array.from(group).filter(c => c.checked).length;
            group.forEach(c => { if (!c.checked) c.disabled = checked >= max; });
        });
    });

    // Countdown
    @if($campaign->show_countdown && $campaign->ends_at)
    const endDate = new Date('{{ $campaign->ends_at->toISOString() }}');
    function updateTimer() {
        const now = new Date();
        const diff = endDate - now;
        if (diff <= 0) { document.getElementById('timer').textContent = 'انتهى التصويت'; return; }
        const days = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const mins = Math.floor((diff % 3600000) / 60000);
        document.getElementById('timer').textContent = `${days} يوم ${hours} ساعة ${mins} دقيقة`;
    }
    updateTimer();
    setInterval(updateTimer, 60000);
    @endif
    </script>
</body>
</html>
