<?php 

class Customer {
    public $email;
    public $phoneNumber;
    public $name;

    function __construct($email = null, $phoneNumber = null, $name = null) {
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->name = $name;
    }
}

?>