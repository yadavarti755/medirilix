<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $isNewUser ? 'Set Your Password' : 'Reset Your Password' }}</title>
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
            background: linear-gradient(135deg, #ea6666 0%, #a24b4b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .email-header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Content */
        .email-content {
            padding: 40px 30px;
        }

        .email-content h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .email-content p {
            font-size: 16px;
            margin-bottom: 15px;
            color: #555555;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .reset-button {
            display: inline-block;
            padding: 15px 35px;
            background: linear-gradient(135deg, #ea6666 0%, #a24b4b 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        /* Alternative link */
        .alternative-link {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .alternative-link p {
            margin-bottom: 10px;
            font-size: 14px;
            color: #6c757d;
        }

        .link-text {
            word-break: break-all;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 12px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #495057;
        }

        /* Warning box */
        .warning-box {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }

        .warning-box h4 {
            color: #856404;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .warning-box h4::before {
            content: "⚠️";
            margin-right: 8px;
        }

        .warning-box ul {
            margin: 10px 0 0 20px;
            color: #856404;
        }

        .warning-box li {
            margin-bottom: 8px;
            font-size: 14px;
        }

        /* Info box */
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }

        .info-box h4 {
            color: #1565c0;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .info-box h4::before {
            content: "ℹ️";
            margin-right: 8px;
        }

        .info-box p {
            color: #1565c0;
            margin-bottom: 0;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e0e0e0, transparent);
            margin: 30px 0;
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

        .company-info {
            font-size: 14px;
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-content {
                padding: 25px 20px;
            }

            .email-header {
                padding: 25px 15px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .reset-button {
                padding: 12px 25px;
                font-size: 14px;
            }

            .alternative-link,
            .warning-box,
            .info-box {
                padding: 15px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1a1a1a;
            }

            .email-content {
                background-color: #1a1a1a;
                color: #ffffff;
            }

            .email-content h2 {
                color: #ffffff;
            }

            .email-content p {
                color: #cccccc;
            }

            .greeting {
                color: #ffffff;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h4>Dear {{ $name }}</h4>
        </div>

        <!-- Content -->
        <div class="email-content">
            <p>
                You have been registered as a user on {{ $siteSettings->site_name ?? config('app.name') }} Website. <br>
                Please click the link below to {{ $isNewUser ? 'set' : 'reset' }} your password:
            </p>

            <!-- Action Button -->
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">
                    {{ $isNewUser ? '🔑 Set My Password' : '🔄 Reset My Password' }}
                </a>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><strong>Button not working?</strong> Copy and paste this link into your browser:</p>
                <div class="link-text">{{ $resetUrl }}</div>
            </div>

            <div class="divider"></div>

            <p>This link is valid for 48 hours.</p>

            <p>
                Note: If your link is expired, you can request a new password reset link by visiting the <a href="{{ url('/login') }}">login page</a> and clicking on "Forgot Password?".
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="company-info">
                {{ $siteSettings->site_name ?? config('app.name') }}
            </div>
            <div style="margin-top: 20px; font-size: 11px; color: #9e9e9e;">
                Email sent on {{ now()->format('F j, Y \a\t g:i A') }}
            </div>
        </div>
    </div>
</body>

</html>