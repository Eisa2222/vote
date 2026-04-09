<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: right; font-size: 12px; }
        h1 { color: #1a1c2e; font-size: 18px; }
        h2 { color: #333; font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: right; }
        th { background: #f8f9fa; font-weight: bold; }
        .stat-box { display: inline-block; width: 23%; padding: 10px; margin: 1%; background: #f8f9fa; text-align: center; border-radius: 4px; }
        .progress-bar { background: #e9ecef; height: 16px; border-radius: 4px; }
        .progress-fill { background: #0d6efd; height: 16px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>تقرير حملة التصويت</h1>
    <p><strong>{{ $campaign->title }}</strong></p>
    <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>

    <h2>الإحصائيات العامة</h2>
    <table>
        <tr>
            <th>إجمالي الروابط</th>
            <th>المصوتين</th>
            <th>لم يصوتوا</th>
            <th>نسبة المشاركة</th>
        </tr>
        <tr>
            <td>{{ $stats['total_links'] }}</td>
            <td>{{ $stats['total_voted'] }}</td>
            <td>{{ $stats['total_not_voted'] }}</td>
            <td>{{ $stats['participation_rate'] }}%</td>
        </tr>
    </table>

    @foreach($campaign->questions as $question)
    <h2>سؤال {{ $question->sort_order }}: {{ $question->title }}</h2>
    <table>
        <tr><th>الخيار</th><th>عدد الأصوات</th><th>النسبة</th></tr>
        @foreach($questionResults[$question->id]['results'] as $result)
        <tr>
            <td>{{ $result['label'] }}</td>
            <td>{{ $result['count'] }}</td>
            <td>{{ $result['percentage'] }}%</td>
        </tr>
        @endforeach
    </table>
    @endforeach
</body>
</html>
