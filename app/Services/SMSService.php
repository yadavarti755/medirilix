<?php

namespace App\Services;

use App\DTO\SmsLogDto;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SMSService
{
    protected string $url;
    protected string $username;
    protected string $pin;
    protected string $signature;
    protected string $entityId;

    protected int $otpTemplateId = 1007386438092372591;
    protected int $dhtiAppVerificationTemplateId = 1007555547146519149;
    protected int $passwordSetupTemplateId = 1007782761809154947;
    protected int $dhtiAppConfirmationTemplateId = 1007784989290988065;

    protected $smsLogService;

    public function __construct()
    {
        // Url
        $this->url        = config('services.sms_gateway.url', 'https://hydgw.sms.gov.in/failsafe/MLink');
        // Username
        $this->username   = config('services.sms_gateway.username', '');
        // Pin
        $this->pin        = config('services.sms_gateway.pin', '');
        // DLT Header / Sender ID
        $this->signature  = config('services.sms_gateway.signature', '');
        // DLT Entity Id
        $this->entityId   = config('services.sms_gateway.dlt_entity_id', '');

        $this->smsLogService = new SmsLogService();
    }


    /**
     * Send OTP SMS.
     *
     * @param  string  $mobileNumber  (10-digit Indian number without country code, e.g. "9xxxxxxxxx")
     * @param  string  $otp
     * @param  string|null $generatedOn  e.g. "31-07-2025 10:35" (optional)
     * @param  bool $asPlainBody  If true, send "text/plain" raw body (matches your cURL). If false, send as form-encoded.
     * @return array ['ok' => bool, 'status' => int, 'body' => string]
     */
    public function sendOtp(string $mobileNumber, string $otp, ?string $generatedOn = null, bool $asPlainBody = true): array
    {
        // Your original message template with DLT variables:
        // "{#var#} OTP for login to CAOMOD website generated on {#var#}. This is valid for 10mins. Please do not share this OTP with any one."
        // Replace variables in order:
        $generatedOn = $generatedOn ?: now()->format('d-m-Y H:i');
        $message = sprintf(
            '%s OTP for login to CAOMOD website generated on %s. This is valid for 10mins. Please do not share this OTP with any one.',
            $otp,
            $generatedOn
        );

        // Build parameter set
        $params = [
            'username'        => $this->username,
            'pin'             => $this->pin,
            'mnumber'         => $mobileNumber, // gateway expects XXXXXXXXXX
            'message'         => $message,
            'signature'       => $this->signature,   // DLT Header (aka "signature")
            'dlt_entity_id'   => $this->entityId,
            'dlt_template_id' => $this->otpTemplateId,
        ];

        return $this->dispatch($params, $asPlainBody);
    }

    /**
     * DHTI Application verifying (OTP)
     * Template: "{#var#} is OTP for verifying your online application to DHTI generated on {#var#}.  This OTP is valid for 10 mins. Please do not share with this OTP with anyone"
     * {#var#} order: [0]=OTP, [1]=GeneratedOn
     */
    public function sendDhtiApplicationOtp(
        string $mobileNumber,
        string $otp,
        ?string $generatedOn = null,
        bool $asPlainBody = true
    ): array {
        $generatedOn = $generatedOn ?: now()->format('d-m-Y H:i');

        // Use the template text EXACTLY as registered on DLT
        $message = sprintf(
            '%s is OTP for verifying your online application to DHTI generated on %s.  This OTP is valid for 10 mins. Please do not share with this OTP with anyone',
            $otp,
            $generatedOn
        );

        $params = [
            'username'        => $this->username,
            'pin'             => $this->pin,
            'mnumber'         => $mobileNumber,
            'message'         => $message,
            'signature'       => $this->signature,
            'dlt_entity_id'   => $this->entityId,
            'dlt_template_id' => $this->dhtiAppVerificationTemplateId,
        ];

        return $this->dispatch($params, $asPlainBody);
    }

    /**
     * Password Setup
     * Template: "Dear {#var#}, you have been registered on CAOMOD website and the link to set/reset your password is {#var#}. Do not share this with anyone."
     * {#var#} order: [0]=RecipientName, [1]=ResetLink
     */
    public function sendPasswordSetup(
        string $mobileNumber,
        string $recipientName,
        string $resetLink,
        bool $asPlainBody = true
    ): array {
        $message = sprintf(
            'Dear %s, you have been registered on CAOMOD website and the link to set/reset your password is %s. Do not share this with anyone.',
            $recipientName,
            'sent on registered email id'
        );

        $params = [
            'username'        => $this->username,
            'pin'             => $this->pin,
            'mnumber'         => $mobileNumber,
            'message'         => $message,
            'signature'       => $this->signature,
            'dlt_entity_id'   => $this->entityId,
            'dlt_template_id' => $this->passwordSetupTemplateId,
        ];

        return $this->dispatch($params, $asPlainBody);
    }

    /**
     * DHTI Application Confirmation
     * Template: "Dear {#var#}, your application has been successfully submitted to DHTI. The application number is {#var#}."
     * {#var#} order: [0]=RecipientName, [1]=ApplicationNumber
     */
    public function sendDhtiApplicationConfirmation(
        string $mobileNumber,
        string $recipientName,
        string $applicationNumber,
        bool $asPlainBody = true
    ): array {
        $message = sprintf(
            'Dear %s, your application has been successfully submitted to DHTI. The application number is %s.',
            $recipientName,
            $applicationNumber
        );

        $params = [
            'username'        => $this->username,
            'pin'             => $this->pin,
            'mnumber'         => $mobileNumber,
            'message'         => $message,
            'signature'       => $this->signature,
            'dlt_entity_id'   => $this->entityId,
            'dlt_template_id' => $this->dhtiAppConfirmationTemplateId,
        ];

        return $this->dispatch($params, $asPlainBody);
    }

    /**
     * Shared request sender (same behavior as in sendOtp).
     */
    protected function dispatch(array $params, bool $asPlainBody): array
    {

        try {
            if ($asPlainBody) {
                $body = str_replace('+', '%20', http_build_query($params));

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'text/plain',
                ])
                    // ->withoutVerifying() // only if you must bypass SSL (avoid in prod)
                    ->withBody($body, 'text/plain')
                    ->withOptions([
                        'verify' => base_path('smsgov.crt'), // ✅ custom CA cert
                    ])
                    ->post($this->url);
            } else {
                $response = \Illuminate\Support\Facades\Http::asForm()
                    ->acceptJson()
                    ->withOptions([
                        'verify' => base_path('smsgov.crt'), // ✅ custom CA cert
                    ])
                    ->post($this->url, $params);
            }

            // SMS log
            $smsLogDto = new SmsLogDto(
                $params['mnumber'],
                'sms',
                $params['message'],
                'sent',
                '',
                $response->status() . ': ' . $response->body(),
                [
                    'message' => $params['message'],
                ],
                date('Y-m-d H:i:s')
            );

            $smsLogResult = $this->smsLogService->create($smsLogDto);
            if (!$smsLogResult) {
                Log::error('Failed to log SMS sent ' . date('Y-m-d H:i:s'));
            }

            Log::info("SMS sending success");

            return [
                'ok'     => $response->successful(),
                'status' => $response->status(),
                'body'   => $response->body(),
            ];
        } catch (\Throwable $e) {
            // SMS log
            $smsLogDto = new SmsLogDto(
                $params['mnumber'],
                'sms',
                $params['message'],
                'failed',
                '',
                'Exception: ' . $e->getMessage(),
                [
                    'message' => $params['message'],
                ],
                date('Y-m-d H:i:s')
            );

            $smsLogResult = $this->smsLogService->create($smsLogDto);
            if (!$smsLogResult) {
                Log::error('Failed to log SMS sent ' . date('Y-m-d H:i:s'));
            }

            Log::info("SMS sending failed");
            return [
                'ok'     => false,
                'status' => 0,
                'body'   => 'Exception: ' . $e->getMessage(),
            ];
        }
    }


    // public static function sendBackendOtpSms($mobile, $otp)
    // {
    //     try {
    //         // Replace with your SMS gateway API
    //         // Example using a generic SMS API
    //         /*
    //         Http::post('https://api.smsgateway.com/send', [
    //             'mobile' => $mobile,
    //             'message' => "Your login OTP is: $otp. Valid for 10 minutes.",
    //             'api_key' => config('services.sms.api_key')
    //         ]);
    //         */

    //         // For testing, you can log the SMS
    //         Log::info("SMS OTP sent to $mobile: $otp");
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send OTP SMS: ' . $e->getMessage());
    //     }
    // }

    // public static function sendOtpSms($mobile, $otp)
    // {
    //     try {
    //         // Replace with your SMS gateway API
    //         // Example using a generic SMS API
    //         /*
    //         Http::post('https://api.smsgateway.com/send', [
    //             'mobile' => $mobile,
    //             'message' => "Your login OTP is: $otp. Valid for 10 minutes.",
    //             'api_key' => config('services.sms.api_key')
    //         ]);
    //         */

    //         // For testing, you can log the SMS
    //         Log::info("SMS OTP sent to $mobile: $otp");
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send OTP SMS: ' . $e->getMessage());
    //     }
    // }
}
