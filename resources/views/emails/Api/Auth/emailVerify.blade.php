<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Verify Your Email Address</h2>
    <p>Thank you for registering! Please verify your email address by clicking the button below.</p>

    <p>
        <a href="{{ $verificationUrl }}" 
           style="display: inline-block; padding: 10px 20px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;">
            Verify Email
        </a>
    </p>

    <p>This verification link will expire in <strong>24 hours</strong>. If you did not request this, please ignore this email.</p>

    <p>Thank you!<br>Team {{ config('app.name') }}</p>
</body>
</html>
