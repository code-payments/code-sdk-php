[![Latest Stable Version](https://poser.pugx.org/code-wallet/client/v)](//packagist.org/packages/code-wallet/client)
[![Total Downloads](https://poser.pugx.org/code-wallet/client/downloads)](//packagist.org/packages/code-wallet/client)
[![Latest Unstable Version](https://poser.pugx.org/code-wallet/client/v/unstable)](//packagist.org/packages/code-wallet/client)
[![License](https://poser.pugx.org/code-wallet/client/license)](//packagist.org/packages/code-wallet/client)
[![PHP Version Require](http://poser.pugx.org/code-wallet/client/require/php)](//packagist.org/packages/code-wallet/client)

# Code Wallet PHP SDK

The Code Wallet PHP SDK is a library that empowers PHP developers to easily integrate Code payments into their applications. With just a few lines of code and minimal setup, you can start accepting payments effortlessly.

See the [documentation](https://code-wallet.github.io/code-sdk/docs/guide/introduction.html) for detailed information.

## What is Code?

[Code](https://getcode.com) is a mobile wallet app that utilizes self-custodial blockchain technology to offer instant, global, and private payment services.

## Installation

You can install the Code Wallet PHP SDK via Composer:

```bash
composer require code-wallet/client
```

## Usage
Here is a quick example demonstrating how to create and verify a payment intent using the PHP SDK:

```php
<?php

require 'vendor/autoload.php';

use CodeWallet\Client\PaymentIntents;

$testData = [
    'destination' => "E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR",
    'amount' => 0.05,
    'currency' => 'usd',
];

// Create a payment request intent
$response = PaymentIntents::create($testData);

// After some time, you can verify the status of the intent
$status = PaymentIntents::getStatus($response['id']);

echo $status;
```

## Getting Help

If you have any questions or need help integrating Code into your website or application, please reach out to us on [Discord](https://discord.gg/DunN9aNS) or [Twitter](https://twitter.com/getcode).

##  Contributing

For now the best way to contribute is to share feedback on [Discord](https://discord.gg/DunN9aNS). This will evolve as we continue to build out the platform and open up more ways to contribute. 
