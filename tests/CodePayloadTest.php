<?php

namespace CodeWallet\Tests;

use CodeWallet\Library\Kin;
use CodeWallet\Library\CodePayload;
use CodeWallet\Library\CodeKind;
use CodeWallet\Errors\ErrInvalidCurrency;
use CodeWallet\Errors\ErrInvalidSize;

use PHPUnit\Framework\TestCase;

class CodePayloadTest extends TestCase {

    private $nonce = "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x10\x11";
    private $fiatAmount = 281474976710911;
    private $kinAmount;
    private $sampleKin = "\x00\x40\x4B\x4C\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x10\x11";
    private $sampleKinAsFiat = "\x02\x00\x88\x13\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x10\x11";
    private $sampleFiat = "\x02\x8c\xFF\x00\x00\x00\x00\x00\x01\x01\x02\x03\x04\x05\x06\x07\x08\x09\x10\x11";

    protected function setUp(): void {
        parent::setUp();
        $this->kinAmount = Kin::fromQuarks(5000000);
    }

    public function testCreateNewPayloadFromParameters() {
        $kind = CodeKind::CASH;
        $amount = 100;
        $payload = new CodePayload($kind, $amount, $this->nonce);
        
        $this->assertEquals($kind, $payload->getKind());
        $this->assertEquals($amount, $payload->getAmount());
        $this->assertEquals($this->nonce, $payload->getNonce());
    }

    public function testInvalidSize() {
        $this->expectException(ErrInvalidSize::class);
        $data = str_repeat("\x00", 19); // Incorrect size
        CodePayload::fromData($data);
    }

    public function testSerializeDeserializeForCashGiftcard() {
        $kind = CodeKind::CASH;
        $amount = 100;
        $payload = new CodePayload($kind, $amount, $this->nonce);
        
        $serialized = $payload->toBinary();
        $deserialized = CodePayload::fromData($serialized);

        $this->assertEquals($kind, $deserialized->getKind());
        $this->assertEquals($amount, $deserialized->getAmount());
        $this->assertEquals($this->nonce, $deserialized->getNonce());
    }

    public function testSerializeDeserializeForRequestPayment() {
        $kind = CodeKind::REQUEST_PAYMENT;
        $amount = 100;
        $currency = 'usd';

        $payload = new CodePayload($kind, $amount, $this->nonce, $currency);

        $serialized = $payload->toBinary();
        $deserialized = CodePayload::fromData($serialized);

        $this->assertEquals($kind, $deserialized->getKind());
        $this->assertEquals($amount, $deserialized->getAmount());
        $this->assertEquals($this->nonce, $deserialized->getNonce());
        $this->assertEquals($currency, $deserialized->getCurrency());
    }

    public function testInvalidCurrency() {
        $this->expectException(ErrInvalidCurrency::class);
        $kind = CodeKind::REQUEST_PAYMENT;
        $amount = 100;
        $currency = 'INVALID'; 

        new CodePayload($kind, $amount, $this->nonce, $currency);
    }

    public function testEncodeKinCash() {
        $amount = $this->kinAmount->toQuarks();
        $payload = new CodePayload(CodeKind::CASH, $amount, $this->nonce);

        $encoded = $payload->toBinary();
        $this->assertEquals($this->sampleKin, $encoded);
    }

    public function testEncodeKinRequest() {
        $amount = intval($this->kinAmount->toDecimal() * 100);
        $payload = new CodePayload(CodeKind::REQUEST_PAYMENT, $amount, $this->nonce, 'kin');

        $encoded = $payload->toBinary();
        $this->assertEquals($this->sampleKinAsFiat, $encoded);
    }

    public function testEncodeFiat() {
        $amount = $this->fiatAmount;
        $payload = new CodePayload(CodeKind::REQUEST_PAYMENT, $amount, $this->nonce, 'usd');

        $encoded = $payload->toBinary();
        $this->assertEquals($this->sampleFiat, $encoded);
    }

}
