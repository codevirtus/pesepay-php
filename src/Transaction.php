<?php

namespace Codevirtus\Payments;

require_once('Amount.php');

class Transaction {
    public $resultUrl;
    public $returnUrl;
    public $merchantReference;
    public $amountDetails;
    public $reasonForPayment;

    public function __construct($amount, $currencyCode, $reasonForPayment, $merchantReference) {
        $this->amountDetails = new Amount($amount, $currencyCode);
        $this->reasonForPayment = $reasonForPayment;
        $this->merchantReference = $merchantReference;
    }
}

?>