<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
</head>
<body style="margin:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:24px 14px;">
        <div style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
            <div style="padding:22px 24px;border-bottom:1px solid #e5e7eb;">
                <div style="font-size:22px;font-weight:700;color:#2c3e50;">{{ config('app.name', 'ShopGram') }}</div>
                @if($campaign->preview_text)
                    <div style="margin-top:8px;font-size:14px;color:#6b7280;">{{ $campaign->preview_text }}</div>
                @endif
            </div>

            @if($campaign->image_path)
                <div style="padding:24px 24px 0;">
                    <img src="{{ url('storage/' . $campaign->image_path) }}" alt="{{ $campaign->subject }}" style="display:block;width:100%;max-width:592px;height:auto;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
            @endif

            <div style="padding:24px;font-size:15px;line-height:1.7;">
                {!! nl2br(e($campaign->body)) !!}
            </div>

            <div style="padding:18px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;line-height:1.6;">
                You are receiving this email because you subscribed to {{ config('app.name', 'ShopGram') }} newsletters.
                <br>
                <a href="{{ URL::signedRoute('newsletter.unsubscribe', $subscriber) }}" style="color:#6b7280;text-decoration:underline;">Unsubscribe</a>
            </div>
        </div>
    </div>
</body>
</html>
