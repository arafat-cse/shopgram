<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        h1, h2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <h1>ShopGram Analytics Report</h1>
    <p>{{ $range['from']->format('d M Y') }} - {{ $range['to']->format('d M Y') }}</p>
    @include('admin.analytics.partials.export-summary')
</body>
</html>
