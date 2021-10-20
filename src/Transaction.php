<?php

namespace Pesepay;

require_once('Amount.php');

class Transaction {
    public $resultUrl;
    public $returnUrl;
    public $merchantReference;
    public $applicationId;
    public $applicationName;
    public $applicationCode;
    public $amountDetails;
    public $transactionType;
    public $reasonForPayment;

    public function __construct($applicationId, $applicationCode, $applicationName, $amount, $currencyCode, $reasonForPayment, $merchantReference) {
        $this->applicationId = $applicationId;
        $this->applicationCode = $applicationCode;
        $this->applicationName = $applicationName;
        $this->amountDetails = new Amount($amount, $currencyCode);
        $this->transactionType = "BASIC";
        $this->reasonForPayment = $reasonForPayment;
        $this->merchantReference = $merchantReference;
    }
}

?>