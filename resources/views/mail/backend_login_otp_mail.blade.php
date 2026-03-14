<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login OTP</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            padding: 20px;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Content */
        .email-content {
            padding: 40px 30px;
            text-align: center;
        }

        .email-content p {
            font-size: 16px;
            margin-bottom: 15px;
            color: #555555;
        }

        /* OTP Box */
        .otp-box {
            background-color: #f8f9fa;
            border: 2px dashed #0d6efd;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            display: inline-block;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 5px;
        }

        /* Footer */
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .email-footer p {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Backend Login OTP</h1>
        </div>
        <div class="email-content">
            <p>You requested a secure login to the administrative area.</p>
            <p>Please use the following One-Time Password (OTP) to proceed:</p>

            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>

            <p>This OTP is valid for 10 minutes. Do not share this code with anyone.</p>
            <p>If you did not request this, please report immediately to the security team.</p>
        </div>
        <div class="email-footer">
            <p>{{ $siteSettings->site_name ?? config('app.name') }}</p>
            <p>&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</body>

</html>