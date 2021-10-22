<?php

namespace Pesepay\Payments;

class ErrorResponse {
    private $success;
    private $message;

    public function __construct($message) {
        $this->success = false;
        $this->message = $message;
    }

    public function success() {
        return $this->success;
    }

    public function message() {
        return $this->message;
    }
}

?>