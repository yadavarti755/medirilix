<x-mail::message>
    # Welcome to {{ $appName }}, {{ $userName }}!

    We're excited to have you on board. Your account has been successfully created and verified.

    ## Account Details

    **Name:** {{ $userName }}
    **Email:** {{ $userEmail }}

    You can now log in to your account and start exploring our services.

    <x-mail::button :url="$loginUrl" color="success">
        Login to Your Account
    </x-mail::button>

    If you have any questions or need assistance, feel free to reach out to our support team.

    Thanks,<br>
    {{ $appName }} Team

    ---

    <x-mail::subcopy>
        If you're having trouble clicking the "Login to Your Account" button, copy and paste the URL below into your web browser:
        [{{ $loginUrl }}]({{ $loginUrl }})

        Need help? Contact us at {{ $supportEmail }}
    </x-mail::subcopy>
</x-mail::message>