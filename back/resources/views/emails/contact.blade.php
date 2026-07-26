<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact form message</title>
</head>
<body style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #000; line-height: 1.5;">
    <h2 style="margin: 0 0 16px;">New contact form message</h2>
    <p><strong>Name:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Phone:</strong> {{ $phone }}</p>
    <p><strong>Message:</strong></p>
    <p style="white-space: pre-wrap;">{{ $body }}</p>
</body>
</html>
