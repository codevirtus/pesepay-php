<?php

namespace Codevirtus\Payments;

include_once('Payment.php');
include_once('Customer.php');
include_once('Amount.php');
include_once('Response.php');
include_once('ErrorResponse.php');
include_once('Transaction.php');

class Pesepay
{
    const PROD_BASE_URL = "https://api.pesepay.com/api/payments-engine";
    const SANDBOX_BASE_URL = "https://api.test.sandbox.pesepay.com/payments-engine";

    const ALGORITHM = 'AES-256-CBC';

    const INIT_VECTOR_LENGTH = 16;

    private $integrationKey;
    private $encryptionKey;
    public $resultUrl;
    public $returnUrl;
    public $isSandbox;

    public function __construct($integrationKey, $encryptionKey, $isSandbox = false)
    {
        $this->integrationKey = $integrationKey;
        $this->encryptionKey = $encryptionKey;
        $this->isSandbox = $isSandbox;
    }

    public function pollTransaction($pollUrl)
    {

        $response = $this->initCurlRequest("GET", $pollUrl);

        if ($response instanceof ErrorResponse)
            return $response;

        if (!isset($response['payload']))
            return new ErrorResponse('Invalid response from PesePay.');

        $decryptedData = $this->decrypt($response['payload']);

        $jsonDecoded = json_decode($decryptedData, true);

        if (!is_array($jsonDecoded))
            return new ErrorResponse('Invalid response from PesePay.');

        $referenceNumber = $jsonDecoded['referenceNumber'];
        $pollUrl = $jsonDecoded['pollUrl'];
        $paid = $jsonDecoded['transactionStatus'] == 'SUCCESS';

        return new Response($referenceNumber, $pollUrl, null, $paid, $jsonDecoded);
    }

    public function checkPayment($referenceNumber)
    {
        $url = $this->checkPaymentUrl() . '?referenceNumber=' . $referenceNumber;
        return $this->pollTransaction($url);
    }

    public function initiateTransaction($transaction)
    {
        if ($this->resultUrl == null)
            throw new \InvalidArgumentException('Result url has not been specified.');

        if ($this->returnUrl == null)
            throw new \InvalidArgumentException('Return url has not been specified.');

        $transaction->resultUrl = $this->resultUrl;
        $transaction->returnUrl = $this->returnUrl;

        $encryptedData = $this->encrypt(json_encode($transaction));

        $payload = json_encode(['payload' => $encryptedData]);

        $response = $this->initCurlRequest("POST", $this->initiatePaymentUrl(), $payload);

        if ($response instanceof ErrorResponse)
            return $response;

        if (!isset($response['payload']))
            return new ErrorResponse('Invalid response from PesePay.');

        $decryptedData = $this->decrypt($response['payload']);

        $jsonDecoded = json_decode($decryptedData, true);

        if (!is_array($jsonDecoded))
            return new ErrorResponse('Invalid response from PesePay.');

        $referenceNumber = $jsonDecoded['referenceNumber'];
        $pollUrl = $jsonDecoded['pollUrl'];
        $redirectUrl = $jsonDecoded['redirectUrl'];

        return new Response($referenceNumber, $pollUrl, $redirectUrl, false, $jsonDecoded);
    }

    public function makeSeamlessPayment($payment, $reasonForPayment, $amount, $requiredFields = null, $merchantReference = null)
    {
        if ($this->resultUrl == null)
            throw new \InvalidArgumentException('Result url has not been specified.');

        $payment->resultUrl = $this->resultUrl;
        $payment->returnUrl = $this->returnUrl;
        $payment->reasonForPayment = $reasonForPayment;
        $payment->amountDetails = new Amount($amount, $payment->currencyCode);
        $payment->merchantReference = $merchantReference;

        $payment->setRequiredFields($requiredFields);

        $encryptedData = $this->encrypt(json_encode($payment));

        $payload = json_encode(['payload' => $encryptedData]);

        $response = $this->initCurlRequest("POST", $this->makeSeamlessPaymentUrl(), $payload);

        if ($response instanceof ErrorResponse)
            return $response;

        if (!isset($response['payload']))
            return new ErrorResponse('Invalid response from PesePay.');

        $decryptedData = $this->decrypt($response['payload']);

        $jsonDecoded = json_decode($decryptedData, true);

        if (!is_array($jsonDecoded))
            return new ErrorResponse('Invalid response from PesePay.');

        $referenceNumber = $jsonDecoded['referenceNumber'];
        $pollUrl = $jsonDecoded['pollUrl'];

        return new Response($referenceNumber, $pollUrl, null, false, $jsonDecoded);
    }

    public function createTransaction($amount, $currencyCode, $paymentReason, $merchantReference = null)
    {
        return new Transaction($amount, $currencyCode, $paymentReason, $merchantReference);
    }

    public function createPayment($currencyCode, $paymentMethodCode, $email, $phone = null, $name = null)
    {

        $customer = new Customer($email, $phone, $name);

        return new Payment($currencyCode, $paymentMethodCode, $customer);
    }

    private function baseUrl()
    {
        return $this->isSandbox ? self::SANDBOX_BASE_URL : self::PROD_BASE_URL;
    }

    private function checkPaymentUrl()
    {
        return $this->baseUrl() . '/v1/payments/check-payment';
    }

    private function initiatePaymentUrl()
    {
        return $this->baseUrl() . '/v1/payments/initiate';
    }

    private function makeSeamlessPaymentUrl()
    {
        return $this->baseUrl() . '/v2/payments/make-payment';
    }

    private function initCurlRequest($requestType, $url, $payload = null)
    {
        $headers = [
            'key: ' . $this->integrationKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'cUrl'
        ]);

        if ($requestType == "POST") {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($curl);

        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($response === false) {
            return new ErrorResponse($curlError ?: 'Failed to connect to PesePay.');
        }

        $result = json_decode($response, true);

        if ($status_code == 200) {
            return is_array($result) ? $result : new ErrorResponse('Invalid response from PesePay.');
        }

        $message = isset($result['message']) ? $result['message'] : 'PesePay request failed with status code ' . $status_code . '.';

        return new ErrorResponse($message);
    }

    /**
     * Encrypt input text by AES-256-CBC algorithm
     *
     * @param string $secretKey 16/24/32 -characters secret key
     * @param string $plainText Text for encryption
     *
     */

    private function encrypt($plainText)
    {
        if (!$this->isKeyLengthValid($this->encryptionKey)) {
            throw new \InvalidArgumentException("Secret key's length must be 128, 192 or 256 bits");
        }

        try {
            // Get initialization vector
            $initVector = substr($this->encryptionKey, 0, self::INIT_VECTOR_LENGTH);
            // Encrypt input text
            $raw = openssl_encrypt(
                $plainText,
                self::ALGORITHM,
                $this->encryptionKey,
                0,
                $initVector
            );
            return $raw;
        } catch (\Exception $e) {
            throw new \RuntimeException('Encryption failed: ' . $e->getMessage());
        }
    }

    /**
     * Decrypt encoded text by AES-256-CBC algorithm
     *
     * @param string $secretKey  16/24/32 -characters secret password
     * @param string $cipherText Encrypted text
     *
     */

    private function decrypt($cipherText)
    {
        if (!$this->isKeyLengthValid($this->encryptionKey)) {
            throw new \InvalidArgumentException("Secret key's length must be 128, 192 or 256 bits");
        }

        try {
            // Get raw encoded data
            $encoded = base64_decode($cipherText);

            // Slice initialization vector using the secret key
            $initVector = substr($this->encryptionKey, 0, self::INIT_VECTOR_LENGTH);

            // Trying to get decrypted text
            $decoded = openssl_decrypt(
                $encoded,
                self::ALGORITHM,
                $this->encryptionKey,
                OPENSSL_RAW_DATA,
                $initVector
            );

            if ($decoded === false) {
                throw new \RuntimeException('Decryption failed: ' . openssl_error_string());
            }

            return $decoded;

        } catch (\Exception $e) {
            throw new \RuntimeException('Decryption failed: ' . $e->getMessage());
        }

    }



    /**
     * Check that secret password length is valid
     *
     * @param string $secretKey 16/24/32 -characters secret password
     *
     * @return bool
     */

    private function isKeyLengthValid($secretKey)
    {
        $length = strlen($secretKey);
        return $length == 16 || $length == 24 || $length == 32;
    }
}
