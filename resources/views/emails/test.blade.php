<!DOCTYPE html>
<html>
<head><title>SMTP Test Email</title></head>
<body style="font-family: Arial, sans-serif; background: #1961f2; padding: 24px;">
    <div style="max-width: 480px; margin: 0 auto; background: #eeeaf5; border-radius: 8px; padding: 24px;">
        <h2 style="margin: 0 0 12px; color: #1b5aec;">Hello</h2>
        <p style="color: #2877e7;">Hello {{ $recipientName }},</p>
        <p style="color: #334155;">This is a test email sent to {{ $recipientEmail }} to confirm your SMTP settings are working correctly.</p>
        <p style="color: #64748b;">Sent at: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p style="color: #94a3b8; font-size: 12px; margin-top: 24px;">MyBlog</p>
    </div>
</body>
</html>