<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Your Account - AbuJaeat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .login-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ff6b35;
            text-align: center;
        }
        .btn {
            display: inline-block;
            background: #ff6b35;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .btn:hover {
            background: #e55a2b;
        }
        .security-note {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Welcome Back!</h1>
        <p>Hello {{ $guestUser->name ?? 'Guest' }}, here's your login link</p>
    </div>

    <div class="content">
        <div class="login-section">
            <h2>Access Your Account</h2>
            <p>Click the button below to securely log into your AbuJaeat account and view your order history.</p>
            
            <a href="{{ $magicLink }}" class="btn">Login to My Account</a>
            
            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                This link will expire in 24 hours for your security.
            </p>
        </div>

        <div class="security-note">
            <h4>🔒 Security Information</h4>
            <ul style="text-align: left;">
                <li>This link is unique to your account</li>
                <li>It will expire after 24 hours</li>
                <li>It can only be used once</li>
                <li>If you didn't request this, please ignore this email</li>
            </ul>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
            <h4>📱 Alternative Access</h4>
            <p>You can also access your account by:</p>
            <ul>
                <li>Using the QR code from your previous order confirmation</li>
                <li>Going to <a href="{{ $dashboardUrl }}">your dashboard</a> and requesting a new login link</li>
            </ul>
        </div>

        <div class="footer">
            <p>Thank you for using AbuJaeat!</p>
            <p>If you have any questions, please contact us at support@abujaeat.com</p>
            <p>© {{ date('Y') }} AbuJaeat. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
