## Installation

You can install the package via composer:

```bash
composer require codevirtus/pesepay
```

### Getting Started
Import the library into your project/application

```js  
const { Pesepay } = require('pesepay')
```

Create an instance of the `Pesepay` class using your integration key and encryption key as supplied by Pesepay.

```php 
$pesepay = new Pesepay("INTEGRATION KEY", "ENCRYPTION KEY");
```

Set return and result urls

```php 
$pesepay->returnUrl = "http://example.com/gateway/return";
$pesepay->resultUrl = "http://example.com/gateway/return";
```

### Make seamless payment

Create the payment 
##### NB: Customer email or number should be provided

```php
$payment = $pesepay->createPayment('CURRECNCY_CODE', 'PAYMENT_METHOD_CODE', 'CUSTOMER_EMAIL(OPTIONAL)', 'CUSTOMER_PHONE_NUMBER(OPTIONAL)', 'CUSTOMER_NAME(OPTIONAL)');
```

Create an `object` of the required fields (if any)
```php
$requiredFields = ['requiredFieldName'=>'requiredFieldValue'];
```

Send of the payment
```php
$response = $pesepay->makeSeamlessPayment($payment, 'Online Transaction', $AMOUNT, $requiredFields);

if ($response->success()) {
    # Save the poll url and reference number (used to check the status of a transaction)
    $referenceNumber = $response->referenceNumber();
    $pollUrl = $response->pollUrl();

} else {
    #Get Error Message
    $errorMessage = $response->message();
}
```

### Make redirect payment
#### Step 1: Initiate a transaction

Create a transaction
```php
$transaction = $pesepay->createTransaction('APP_ID', 'APP_CODE', 'APP_NAME', $amount, 'CURRENCY_CODE', 'PAYMENT_REASON');
```

Initiate the transaction
```php
$response = $pesepay->initiateTransaction($transaction);

if ($response->success()) {
    # Save the reference number (used to check the status of a transaction and to make the payment)
    $referenceNumber = $response->referenceNumber();
    # Get the redirect url and use it as you see fit   
    $redirectUrl = $response->redirectUrl();
    
} else {
    # Get error message
    $errorMessage = $response->message();
}
```

#### Step 2: Make the payment

Create the payment 
##### NB: Customer email or number should be provided

```php
$payment = $pesepay->createPayment('CURRECNCY_CODE', 'PAYMENT_METHOD_CODE', 'CUSTOMER_EMAIL(OPTIONAL)', 'CUSTOMER_PHONE_NUMBER(OPTIONAL)', 'CUSTOMER_NAME(OPTIONAL)');
```

Create a `object` of the required fields (if any)

```php
$requiredFields = ['requiredFieldName'=>'requiredFieldValue'];
```

Send of the payment
```php
$response = $pesepay->makePayment($payment, $referenceNumber, $requiredFields);

if ($response->success()) {
    # Save the poll url (used to check the status of a transaction)
    $pollUrl = $response->pollUrl();
    
} else {
    # Get error message
    $errorMessage = $response->message();
}
```

### Check Payment Status
#### Method 1: Check using reference number
```php
$response = $pesepay->checkPayment($referenceNumber);

if ($response->success()) {

    if ($response->paid()) {
        # Payment was successfull
    }

} else {
    # Get error message
    $errorMessage = $response->message();
}
```
#### Method 2: Check using poll url
```php
$response = $pesepay->pollTransaction($pollUrl);

if ($response->success()) {

    if ($response->paid()) {
        # Payment was successfull
    }

} else {
    # Get error message
    $errorMessage = $response->message();
}
```