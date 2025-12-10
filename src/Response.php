<?php

namespace Codevirtus\Payments;

class Response
{
    private $success;
    private $referenceNumber;
    private $pollUrl;
    private $redirectUrl;
    private $paid;
    private $data = [];
    private $amountDetails = [];
    private $transactionMetadata = [];

    public function __construct($referenceNumber, $pollUrl, $redirectUrl = null, $paid = false, array $data = [])
    {
        $this->success = true;
        $this->referenceNumber = $referenceNumber;
        $this->pollUrl = $pollUrl;
        $this->redirectUrl = $redirectUrl;
        $this->paid = (bool) $paid;

        $this->data = $data;
        $this->amountDetails = isset($data['amountDetails']) && is_array($data['amountDetails']) ? $data['amountDetails'] : [];
        $this->transactionMetadata = isset($data['transactionMetadata']) && is_array($data['transactionMetadata']) ? $data['transactionMetadata'] : [];
    }

    public function success()
    {
        return $this->success;
    }

    public function pollUrl()
    {
        return $this->pollUrl;
    }

    public function referenceNumber()
    {
        return $this->referenceNumber;
    }

    public function redirectUrl()
    {
        return $this->redirectUrl;
    }

    public function paid()
    {
        return $this->paid;
    }

    public function rawData()
    {
        return $this->data;
    }

    // top-level fields
    public function dateOfTransaction()
    {
        return isset($this->data['dateOfTransaction']) ? $this->data['dateOfTransaction'] : null;
    }

    public function applicationId()
    {
        return isset($this->data['applicationId']) ? $this->data['applicationId'] : null;
    }

    public function applicationName()
    {
        return isset($this->data['applicationName']) ? $this->data['applicationName'] : null;
    }

    public function reasonForPayment()
    {
        return isset($this->data['reasonForPayment']) ? $this->data['reasonForPayment'] : null;
    }

    public function transactionStatus()
    {
        return isset($this->data['transactionStatus']) ? $this->data['transactionStatus'] : null;
    }

    public function transactionStatusCode()
    {
        return isset($this->data['transactionStatusCode']) ? $this->data['transactionStatusCode'] : null;
    }

    public function transactionStatusDescription()
    {
        return isset($this->data['transactionStatusDescription']) ? $this->data['transactionStatusDescription'] : null;
    }

    public function resultUrl()
    {
        return isset($this->data['resultUrl']) ? $this->data['resultUrl'] : null;
    }

    public function returnUrl()
    {
        return isset($this->data['returnUrl']) ? $this->data['returnUrl'] : null;
    }

    public function amountDetails()
    {
        return $this->amountDetails;
    }

    public function amount()
    {
        return isset($this->amountDetails['amount']) ? $this->amountDetails['amount'] : null;
    }

    public function currencyCode()
    {
        return isset($this->amountDetails['currencyCode']) ? $this->amountDetails['currencyCode'] : null;
    }

    public function defaultCurrencyAmount()
    {
        return isset($this->amountDetails['defaultCurrencyAmount']) ? $this->amountDetails['defaultCurrencyAmount'] : null;
    }

    public function defaultCurrencyCode()
    {
        return isset($this->amountDetails['defaultCurrencyCode']) ? $this->amountDetails['defaultCurrencyCode'] : null;
    }

    public function transactionServiceFee()
    {
        return isset($this->amountDetails['transactionServiceFee']) ? $this->amountDetails['transactionServiceFee'] : null;
    }

    public function customerPayableAmount()
    {
        return isset($this->amountDetails['customerPayableAmount']) ? $this->amountDetails['customerPayableAmount'] : null;
    }

    public function totalTransactionAmount()
    {
        return isset($this->amountDetails['totalTransactionAmount']) ? $this->amountDetails['totalTransactionAmount'] : null;
    }

    public function merchantAmount()
    {
        return isset($this->amountDetails['merchantAmount']) ? $this->amountDetails['merchantAmount'] : null;
    }

    public function formattedMerchantAmount()
    {
        return isset($this->amountDetails['formattedMerchantAmount']) ? $this->amountDetails['formattedMerchantAmount'] : null;
    }

    public function transactionMetadata()
    {
        return $this->transactionMetadata;
    }

    public function metadataCode()
    {
        return isset($this->transactionMetadata['code']) ? $this->transactionMetadata['code'] : null;
    }

    public function metadataQrCode()
    {
        return isset($this->transactionMetadata['qrCode']) ? $this->transactionMetadata['qrCode'] : null;
    }
}

?>