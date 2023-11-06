<?php

use PHPUnit\Framework\TestCase;
use CodeWallet\Client\PaymentIntents;
use CodeWallet\Library\PaymentRequestIntent;

class PaymentIntentsTest extends TestCase
{
    private $paymentIntents;

    public function testCreate()
    {
        $testData = [
            'destination' => "E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR",
            'amount' => 0.05,
            'currency' => 'usd',
        ];

        // Assuming that `create` method in PaymentIntents class returns array with 'clientSecret' and 'id'
        $response = PaymentIntents::create($testData);

        $testData['clientSecret'] = $response['clientSecret'];
        $expectedIntent = new PaymentRequestIntent($testData);

        // Verifying both have the same id
        $this->assertEquals($response['id'], $expectedIntent->get_intent_id());
    }
}