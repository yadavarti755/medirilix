<x-mail::message>
    # Hello, {{ $userName }}!

    Thank you for registering with {{ $appName }}. To complete your registration, please verify your email address using the verification code below.

    <x-mail::panel>
        ## Your Verification Code

        <div style="font-size: 32px; font-weight: bold; color: #4CAF50; letter-spacing: 5px; text-align: center; padding: 20px 0;">
            {{ $verificationCode }}
        </div>
    </x-mail::panel>

    **Important Information:**
    - This code will expire in **{{ $expiryMinutes }} minutes**
    - Enter this code on the verification page to activate your account
    - Do not share this code with anyone

    <x-mail::button :url="$verifyUrl" color="primary">
        Verify Your Account
    </x-mail::button>

    If you didn't create an account with {{ $appName }}, please ignore this email or contact our support team immediately.

    Thanks,<br>
    {{ $appName }} Team

    ---

    <x-mail::subcopy>
        **Security Tip:** We will never ask you for your password via email. If you receive any suspicious emails, please report them to {{ $supportEmail }}.

        If you're having trouble clicking the "Verify Your Account" button, copy and paste the URL below into your web browser:
        [{{ $verifyUrl }}]({{ $verifyUrl }})
    </x-mail::subcopy>
</x-mail::message>