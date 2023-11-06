<?php
namespace CodeWallet\Tests;

use CodeWallet\Library\PaymentRequestIntent;
use CodeWallet\Errors\ErrDestinationRequired;

use PHPUnit\Framework\TestCase;

class PaymentRequestIntentTest extends TestCase {
    protected $destination;

    protected function setUp(): void {
        $this->destination = '11111111111111111111111111111111';
    }

    public function testConstructor() {
        $intent = new PaymentRequestIntent([
            'destination' => $this->destination,
            'amount' => 10,
            'currency' => 'kin'
        ]);

        $this->assertEquals($this->destination, $intent->options['destination']);
        $this->assertEquals(10, $intent->options['amount']);
        $this->assertEquals('kin', $intent->options['currency']);
        $this->assertEquals(10 * 100, $intent->convertedAmount);

        $intent2 = new PaymentRequestIntent([
            'destination' => $this->destination,
            'amount' => 10,
            'currency' => 'usd'
        ]);

        $this->assertEquals($this->destination, $intent2->options['destination']);
        $this->assertEquals(10, $intent2->options['amount']);
        $this->assertEquals('usd', $intent2->options['currency']);
        $this->assertEquals(10 * 100, $intent2->convertedAmount);
    }

    public function testValidate() {
        $this->expectException(ErrDestinationRequired::class);
        new PaymentRequestIntent([
            'amount' => 10,
            'currency' => 'kin'
        ]);

        $this->expectException(ErrAmountRequired::class);
        new PaymentRequestIntent([
            'destination' => $this->destination,
            'currency' => 'kin'
        ]);

        $this->expectException(ErrCurrencyRequired::class);
        new PaymentRequestIntent([
            'destination' => $this->destination,
            'amount' => 10,
        ]);

        $this->expectException(ErrInvalidCurrency::class);
        new PaymentRequestIntent([
            'destination' => $this->destination,
            'amount' => 10,
            'currency' => 'invalidCurrency'
        ]);
    }

    public function testSign() {
        $intent = new PaymentRequestIntent([
            'destination' => $this->destination,
            'amount' => 10,
            'currency' => 'usd',
            'idempotencyKey' => '1234'
        ]);

        $actual = $intent->sign();

        $expected = [
            'message' => [
                10, 34, 10, 32,  0,  0,  0,   0,   0,   0,  0,
                0,  0,  0,  0,  0,  0,  0,   0,   0,   0,  0,
                0,  0,  0,  0,  0,  0,  0,   0,   0,   0,  0,
                0,  0,  0, 26, 14, 10,  3, 117, 115, 100, 17,
                0,  0,  0,  0,  0,  0, 36,  64
            ],
            'intent' => 'GHEXGTE2r1PartuDip4VhDz8b2RY4xqRTRtMCUEaEXXN',
            'signature' => [
                103, 103, 195, 242,   9,  66, 226,  48,  98, 182,  94,
                172, 255,  84, 166,  93, 138, 175, 245, 162, 121,  68,
                236,  16, 142,  46, 221, 160, 161,  70, 224,  49,  50,
                66,  74,  43, 247,  39,  69, 179, 130,  15, 140, 178,
                59, 255,  47, 104,  56,  75,  75, 193, 226,   2, 251,
                52, 183,   8,  41, 236, 218, 205,  21,  14
            ]
        ];

        $this->assertEquals($expected['intent'], $actual['intent']);
        $this->assertEquals($expected['message'], array_map('ord', str_split($actual['message'])));
        $this->assertEquals($expected['signature'], array_map('ord', str_split($actual['signature'])));
    }

    public function testProtoSerialization1() {
        $destination = "11111111111111111111111111111111";

        $options = [
            'destination' => $destination,
            'amount' => 10,
            'currency' => 'usd'
        ];

        $intent = new PaymentRequestIntent($options);
        $actual = $intent->to_proto();

        $expected = [
            0x2A, 0x34, 0x0A, 0x22, 0x0A, 0x20, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x1A, 0x0E, 0x0A, 0x03, 0x75, 0x73, 0x64, 0x11, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x24, 0x40
        ];

        $this->assertEquals($expected, array_map('ord', str_split($actual)));
    }

    public function testProtoSerialization2() {
        $destination = "11111111111111111111111111111111";

        $options = [
            'destination' => $destination,
            'amount' => 10,
            'currency' => 'kin'
        ];

        $intent = new PaymentRequestIntent($options);
        $actual = $intent->to_proto();

        $expected = [
            0x2a, 0x41, 0x0a, 0x22, 0x0a, 0x20, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x12, 0x1b, 0x0a, 0x03, 0x6b, 0x69,
            0x6e, 0x11, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0xf0, 0x3f, 0x19,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x24, 0x40, 0x20, 0xc0, 0x84,
            0x3d
        ];

        $this->assertEquals($expected, array_map('ord', str_split($actual)));
    }

    public function testProtoSerialization3() {
        $destination = "2q7pyhPwAwZ3QMfZrnAbDhnh9mDUqycszcpf86VgQxhF";

        $options = [
            'destination' => $destination,
            'amount' => 54321.6789,
            'currency' => 'kin'
        ];

        $intent = new PaymentRequestIntent($options);
        $actual = $intent->to_proto();

        $expected = [
            0x2a, 0x43, 0x0a, 0x22, 0x0a, 0x20, 0x1b, 0x2f, 0x49, 0x09, 0x6e,
            0x3e, 0x5d, 0xbd, 0x0f, 0xcf, 0xa9, 0xc0, 0xc0, 0xcd, 0x92, 0xd9,
            0xab, 0x3b, 0x21, 0x54, 0x4b, 0x34, 0xd5, 0xdd, 0x4a, 0x65, 0xd9,
            0x8b, 0x87, 0x8b, 0x99, 0x22, 0x12, 0x1d, 0x0a, 0x03, 0x6b, 0x69,
            0x6e, 0x11, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0xf0, 0x3f, 0x19,
            0x29, 0x5c, 0x8f, 0xc2, 0x35, 0x86, 0xea, 0x40, 0x20, 0xc0, 0x9c,
            0xa1, 0x9e, 0x14
        ];

        $this->assertEquals($expected, array_map('ord', str_split($actual)));
    }

}