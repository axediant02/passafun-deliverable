<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .content {
            padding: 30px 20px;
            text-align: center;
        }
        .otp-code {
            font-size: 32px;
            letter-spacing: 4px;
            color: #1976D2;
            font-weight: bold;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        .login-button {
            text-align: center;
            margin: 25px 0;
        }

        .button {
            background-color: #1976D2;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #1565C0;
        }
        .warning {
            color: #dc3545;
            font-size: 14px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #1976D2; margin: 0;">Password Reset</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            <p>You have requested to reset your password. Please use the following OTP to proceed:</p>
            
            <div class="otp-code">
                {{ $otp }}
            </div>
            
            <p>This OTP will expire in 5 minutes.</p>

            <div class="login-button">
                <a href="https://dev.studio.passafund.com" class="button">
                    Return to Login Page
                </a>
            </div>
            
            <div class="warning">
                If you didn't request this password reset, please ignore this email or contact support.
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply.</p>
            <p>&copy; {{ date('Y') }} PassaQuiz Studio. All rights reserved.</p>
        </div>
    </div>
</body>
</html>