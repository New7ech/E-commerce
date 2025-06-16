<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; margin: 20px 0; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 3px; }
        .button:hover { background-color: #0056b3; }
        p { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p>
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </p>
        <p>This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <hr>
        <p><small>If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>{{ $resetUrl }}</small></p>
    </div>
</body>
</html>
