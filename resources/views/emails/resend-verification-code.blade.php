<x-mail::message>
    # Hello Again, {{ $userName }}!

    You requested a new verification code for your {{ $appName }} account. Here's your new code:

    <x-mail::panel>
        ## Your New Verification Code

        <div style="font-size: 32px; font-weight: bold; color: #FF9800; letter-spacing: 5px; text-align: center; padding: 20px 0;">
            {{ $verificationCode }}
        </div>
    </x-mail::panel>

    **Important Information:**
    - This is your **new** verification code
    - Any previous codes are now invalid
    - This code will expire in **{{ $expiryMinutes }} minutes**
    - Enter this code on the verification page to activate your account

    <x-mail::button :url="$verifyUrl" color="primary">
        Verify Your Account Now
    </x-mail::button>

    If you didn't request a new verification code, please secure your account immediately and contact our support team.

    Thanks,<br>
    {{ $appName }} Team

    ---

    <x-mail::subcopy>
        **Need Help?** If you're experiencing issues with verification, please contact us at {{ $supportEmail }}.

        If you're having trouble clicking the "Verify Your Account Now" button, copy and paste the URL below into your web browser:
        [{{ $verifyUrl }}]({{ $verifyUrl }})
    </x-mail::subcopy>
</x-mail::message>