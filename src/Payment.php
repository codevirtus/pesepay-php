<?php

class Payment {
    public $currencyCode;
    public $paymentMethodCode;
    public $customer;
    public $referenceNumber;
    public $amountDetails;
    public $reasonForPayment;
    public $paymentRequestFields;
    public $paymentMethodRequiredFields;
    public $merchantReference;
    public $returnUrl;
    public $resultUrl;

    function __construct($currencyCode, $paymentMethodCode, $customer) {
        $this->currencyCode = $currencyCode;
        $this->paymentMethodCode = $paymentMethodCode;
        $this->customer = $customer;
    }
    
    public function setRequiredFields($requiredFiels) {
        $this->paymentRequestFields = $requiredFiels;
        $this->paymentMethodRequiredFields = $requiredFiels;
    }
}

?>