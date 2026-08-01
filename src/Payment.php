<?php

namespace Codevirtus\Payments;

class Payment {
    const SPLIT_AMOUNT_MODE_PRINCIPAL = 'PRINCIPAL';
    const SPLIT_AMOUNT_MODE_ADD_ON = 'ADD_ON';

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
    public $paymentMetadata;

    function __construct($currencyCode, $paymentMethodCode, $customer) {
        $this->currencyCode = $currencyCode;
        $this->paymentMethodCode = $paymentMethodCode;
        $this->customer = $customer;
    }
    
    public function setRequiredFields($requiredFiels) {
        $this->paymentRequestFields = $requiredFiels;
        $this->paymentMethodRequiredFields = $requiredFiels;
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