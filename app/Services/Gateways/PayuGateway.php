<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class PayuGateway implements PaymentGatewayInterface
{
    protected $merchantKey;
    protected $salt;
    protected $baseUrl;

    public function __construct($credentials, $testMode = false)
    {
        $this->merchantKey = $credentials['merchant_key'] ?? '';
        $this->salt = $credentials['salt_key'] ?? '';
        // Adjust URL based on test mode if PayU supports it differently via URL
        $this->baseUrl = Config::get('constants.payu.PAYMENT_URL');
    }

    public function initiate(array $data)
    {
        $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
        $hashVarsSeq = explode('|', $hashSequence);
        $hashString = '';

        $data['key'] = $this->merchantKey; // Ensure key is present

        foreach ($hashVarsSeq as $hash_var) {
            $hashString .= isset($data[$hash_var]) ? $data[$hash_var] : '';
            $hashString .= '|';
        }

        $hashString .= $this->salt;
        $hash = strtolower(hash('sha512', $hashString));

        $data['hash'] = $hash;
        $data['action'] = $this->baseUrl;

        // PayU expects a form POST, so we return data to populate the form
        return [
            'status' => true,
            'type' => 'form_post',
            'url' => $this->baseUrl,
            'data' => $data // Contains hash, txnid, amount, etc.
        ];
    }

    public function processCallback(array $data)
    {
        $status = $data['status'];
        $firstname = $data['firstname'];
        $amount = $data['amount'];
        $txnid = $data['txnid'];
        $posted_hash = $data['hash'];
        $key = $data['key'];
        $productinfo = $data['productinfo'];
        $email = $data['email'];
        $udf1 = $data['udf1']; // order_number

        if (isset($data['additionalCharges'])) {
            $additionalCharges = $data['additionalCharges'];
            $retHashSeq = $additionalCharges . '|' . $this->salt . '|' . $status . '||||||||||' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {
            $retHashSeq = $this->salt . '|' . $status . '||||||||||' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }

        $hash = strtolower(hash('sha512', $retHashSeq));

        if ($hash != $posted_hash) {
            return [
                'status' => false,
                'message' => 'Invalid Transaction. Please try again',
                'payment_status' => 'FAILED'
            ];
        }

        return [
            'status' => true,
            'transaction_id' => $txnid,
            'amount' => $amount,
            'payment_status' => $status == 'success' ? 'COMPLETED' : 'FAILED',
            'raw_response' => $data
        ];
    }
}
