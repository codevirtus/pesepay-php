<?php

class Response {
    private $success;
    private $referenceNumber;
    private $pollUrl;
    private $redirectUrl;

    public function __construct($referenceNumber, $pollUrl, $redirectUrl = null) {
        $this->success = true;
        $this->referenceNumber = $referenceNumber;
        $this->pollUrl = $pollUrl;
        $this->redirectUrl = $redirectUrl;
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

    public function redirectUrl() {
        return $this->redirectUrl;
    }
}

?>