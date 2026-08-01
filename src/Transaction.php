<?php

namespace Codevirtus\Payments;

require_once('Amount.php');

class Transaction {
    const SPLIT_AMOUNT_MODE_PRINCIPAL = 'PRINCIPAL';
    const SPLIT_AMOUNT_MODE_ADD_ON = 'ADD_ON';

    public $resultUrl;
    public $returnUrl;
    public $merchantReference;
    public $amountDetails;
    public $reasonForPayment;
    public $paymentMetadata;

    public function __construct($amount, $currencyCode, $reasonForPayment, $merchantReference) {
        $this->amountDetails = new Amount($amount, $currencyCode);
        $this->reasonForPayment = $reasonForPayment;
        $this->merchantReference = $merchantReference;
    }

    public function setPaymentMetadata(array $paymentMetadata) {
        $this->paymentMetadata = $paymentMetadata;
    }

    public function setSplitPayment($beneficiaryMerchantEmail, $splitAmountMode = self::SPLIT_AMOUNT_MODE_PRINCIPAL) {
        if ($beneficiaryMerchantEmail == null || !filter_var($beneficiaryMerchantEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('A valid beneficiary merchant email is required for split payments.');
        }

        $this->paymentMetadata = [
            'beneficiaryMerchantEmail' => $beneficiaryMerchantEmail,
            'splitAmountMode' => $this->normalizeSplitAmountMode($splitAmountMode)
        ];
    }

    private function normalizeSplitAmountMode($splitAmountMode) {
        $mode = strtoupper($splitAmountMode);

        $aliases = [
            'PRINCIPAL' => self::SPLIT_AMOUNT_MODE_PRINCIPAL,
            'PRINCIPAL_AMOUNT' => self::SPLIT_AMOUNT_MODE_PRINCIPAL,
            'ADD_ON' => self::SPLIT_AMOUNT_MODE_ADD_ON,
            'ADDON' => self::SPLIT_AMOUNT_MODE_ADD_ON,
            'ADDED_ON_TOP' => self::SPLIT_AMOUNT_MODE_ADD_ON,
            'ON_TOP' => self::SPLIT_AMOUNT_MODE_ADD_ON
        ];

        if (!isset($aliases[$mode])) {
            throw new \InvalidArgumentException("Invalid split amount mode '{$splitAmountMode}'. Use PRINCIPAL or ADD_ON.");
        }

        return $aliases[$mode];
    }
}

?>