<?php

class Response {
    private $success;
    private $referenceNumber;
    private $pollUrl;

    public function __construct($referenceNumber, $pollUrl) {
        $this->success = true;
        $this->referenceNumber = $referenceNumber;
        $this->pollUrl = $pollUrl;
    }

    public function success() {
        return $this->success;
    }

    public function pollUrl() {
        return $this->pollUrl;
    }

    public function referenceNumber() {
        return $this->referenceNumber;
    }
}

?>