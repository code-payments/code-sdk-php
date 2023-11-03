<?php
use PHPUnit\Framework\TestCase;
use CodeWallet\Library\PublicKey;

class PublicKeyTest extends TestCase {

    public function testFromBase58() {
        $base58Key = PublicKey::fromBase58('CiDwVBFgWV9E5MvXWoLgnEgn2hK7rJikbvfWavzAQz3');
        $expectedBytes = hex2bin('0300000000000000000000000000000000000000000000000000000000000000'); // This is a hexadecimal representation of [3] + [0]*31

        $this->assertEquals($base58Key->toBytes(), $expectedBytes);
    }

    public function testToBase58() {
        $key = PublicKey::fromBase58('CiDwVBFgWV9E5MvXWoLgnEgn2hK7rJikbvfWavzAQz3');
        $this->assertEquals($key->toBase58(), 'CiDwVBFgWV9E5MvXWoLgnEgn2hK7rJikbvfWavzAQz3');

        $key2 = PublicKey::fromBase58('11111111111111111111111111111111');
        $this->assertEquals($key2->toBase58(), '11111111111111111111111111111111');
    }

    public function testToBytes() {
        $key = PublicKey::fromBase58('CiDwVBFgWV9E5MvXWoLgnEgn2hK7rJikbvfWavzAQz3');
        $this->assertEquals(strlen($key->toBytes()), 32);
        $this->assertEquals($key->toBase58(), 'CiDwVBFgWV9E5MvXWoLgnEgn2hK7rJikbvfWavzAQz3');

        $key2 = PublicKey::fromBase58('11111111111111111111111111111111');
        $this->assertEquals(strlen($key2->toBytes()), 32);
        $this->assertEquals($key2->toBase58(), '11111111111111111111111111111111');
    }
}

?>