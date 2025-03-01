<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
</head>
<body>
    <h1>Hello,</h1>
    <p>Your OTP code for login into your account is <strong>{{ $otp ?? 'NA' }}</strong>.</p>
    <p>This code will expire at <strong>{{ $expires_at ?? 'NA' }}</strong>.</p>
    <p>If you did not request otp, please ignore this email.</p>
</body>
</html>
