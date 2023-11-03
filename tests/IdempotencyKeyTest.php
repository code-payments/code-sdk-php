<?php

namespace CodeWallet\Tests;

use PHPUnit\Framework\TestCase;
use StephenHill\Base58;

use CodeWallet\Errors\ErrInvalidSize;
use CodeWallet\Library\IdempotencyKey;

class IdempotencyKeyTest extends TestCase {

    public function testCreateIdempotencyKeyFromBytearray() {
        $key = new IdempotencyKey();
        $this->assertEquals(strlen($key->__toString()), 11);
    }

    public function testThrowErrInvalidSizeForBytearray() {
        $this->expectException(ErrInvalidSize::class);
        new IdempotencyKey(str_repeat(chr(0), 10));
    }

    public function testCreateIdempotencyKeyFromClientSecret() {
        $data = pack("C*", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        $base58 = new Base58();
        $clientSecret = $base58->encode($data);
        $key = IdempotencyKey::fromClientSecret($clientSecret);
        $this->assertEquals(strlen($key->getRawValue()), 11);
        $this->assertEquals($key->getRawValue(), $data);
    }

    public function testCreateIdempotencyKeyFromString() {
        $testString = 'test_string';
        $key = IdempotencyKey::fromSeed($testString);
        $this->assertEquals(strlen($key->getRawValue()), 11);

        $expectedArray = pack("C*", 0x4b, 0x64, 0x1e, 0x9a, 0x92, 0x3d, 0x1e, 0xa5, 0x7e, 0x18, 0xfe);
        $this->assertEquals($key->getRawValue(), $expectedArray);
    }
}

?>