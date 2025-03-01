<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>

    <p>
        <a href="{{ $resetUrl }}"
           style="display: inline-block; padding: 10px 20px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;">
            Reset Password
        </a>
    </p>

    <p>This password reset link will expire in <strong>24 hours</strong>. If you did not request a password reset, no further action is required.</p>

    <p>Thank you!<br>Team {{ config('app.name') }}</p>
</body>
</html>
