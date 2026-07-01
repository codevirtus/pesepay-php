# PesePay PHP SDK

A simple PHP SDK for integrating **PesePay** payments into your application.

---

# Installation

Install the package via Composer:

```bash
composer require codevirtus/pesepay
```

---

# Getting Started

## 1. Import the SDK

```php
require_once 'vendor/autoload.php';

use Codevirtus\Payments\Pesepay;
```

## 2. Initialize the Client

Create a new `Pesepay` instance using the credentials provided by PesePay.

```php
$pesepay = new Pesepay(
    "INTEGRATION_KEY",
    "ENCRYPTION_KEY"
);
```

## 3. Configure Callback URLs

Set the URLs that PesePay will use after processing a payment.

```php
$pesepay->returnUrl = "https://example.com/gateway/return";
$pesepay->resultUrl = "https://example.com/gateway/result";
```

* **returnUrl** – Where the customer is redirected after completing payment.
* **resultUrl** – Endpoint that receives the payment result.

---

# Seamless Payments

Seamless payments allow customers to complete payment directly within your application.

## Step 1: Create a Payment

> **Note:** Either the customer's email address or phone number must be provided.

```php
$payment = $pesepay->createPayment(
    'CURRENCY_CODE',
    'PAYMENT_METHOD_CODE',
    'CUSTOMER_EMAIL',          // Optional
    'CUSTOMER_PHONE_NUMBER',   // Optional
    'CUSTOMER_NAME'            // Optional
);
```

---

## Step 2: Provide Required Payment Fields

Different payment methods require different fields.

### Visa

```php
$requiredFields = [
    "creditCardExpiryDate" => "09/23",
    "creditCardNumber" => "4867960000005461",
    "creditCardSecurityNumber" => "608"
];
```

### Mobile Money (EcoCash, InnBucks, etc.)

```php
$requiredFields = [
    "customerPhoneNumber" => "0712345678"
];
```

---

## Step 3: Submit the Payment

```php
$response = $pesepay->makeSeamlessPayment(
    $payment,
    'Online Transaction',
    $AMOUNT,
    $requiredFields,
    'MERCHANT_REFERENCE' // Optional
);

if ($response->success()) {

    // Store these values to check payment status later
    $referenceNumber = $response->referenceNumber();
    $pollUrl = $response->pollUrl();

    // Transaction details
    $amount = $response->amount();
    $currencyCode = $response->currencyCode();
    $transactionFee = $response->transactionServiceFee();
    $metaData = $response->transactionMetadata();

    // Full API response
    $data = $response->rawData();

    // InnBucks-specific data
    $code = $response->metadataCode();
    $qrcode = $response->metadataQrCode();

} else {

    $errorMessage = $response->message();

}
```

---

# Redirect Payments

Redirect payments send the customer to the PesePay checkout page to complete payment.

## Step 1: Create a Transaction

```php
$transaction = $pesepay->createTransaction(
    $amount,
    'CURRENCY_CODE',
    'PAYMENT_REASON',
    'MERCHANT_REFERENCE' // Optional
);
```

---

## Step 2: Initiate the Transaction

```php
$response = $pesepay->initiateTransaction($transaction);

if ($response->success()) {

    // Save these values for future payment status checks
    $referenceNumber = $response->referenceNumber();
    $pollUrl = $response->pollUrl();

    // Redirect the customer
    $redirectUrl = $response->redirectUrl();

} else {

    $errorMessage = $response->message();

}
```

---

# Checking Payment Status

You can verify a payment using either the transaction reference number or the poll URL returned when the payment was created.

## Option 1: Check by Reference Number

```php
$response = $pesepay->checkPayment($referenceNumber);

if ($response->success()) {

    if ($response->paid()) {
        // Payment successful
    }

} else {

    $errorMessage = $response->message();

}
```

---

## Option 2: Check by Poll URL

```php
$response = $pesepay->pollTransaction($pollUrl);

if ($response->success()) {

    if ($response->paid()) {
        // Payment successful
    }

} else {

    $errorMessage = $response->message();

}
```

---

# Response Methods

Most SDK responses provide the following helper methods:

| Method                    | Description                                                    |
| ------------------------- | -------------------------------------------------------------- |
| `success()`               | Returns `true` if the request completed successfully.          |
| `message()`               | Returns the error message if the request failed.               |
| `referenceNumber()`       | Returns the PesePay transaction reference.                     |
| `pollUrl()`               | Returns the polling URL used to check payment status.          |
| `amount()`                | Returns the transaction amount.                                |
| `currencyCode()`          | Returns the transaction currency.                              |
| `transactionServiceFee()` | Returns the service fee charged.                               |
| `transactionMetadata()`   | Returns additional transaction metadata.                       |
| `rawData()`               | Returns the complete API response.                             |
| `metadataCode()`          | Returns the InnBucks payment code (where applicable).          |
| `metadataQrCode()`        | Returns the InnBucks QR code (where applicable).               |
| `redirectUrl()`           | Returns the checkout URL for redirect payments.                |
| `paid()`                  | Returns `true` if the payment has been completed successfully. |
