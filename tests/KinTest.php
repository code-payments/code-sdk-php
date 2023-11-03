<?php

namespace CodeWallet\Tests;

use CodeWallet\Library\Kin;
use PHPUnit\Framework\TestCase;

class KinTest extends TestCase {

    public function testFromQuarks() {
        $this->assertEquals(1, Kin::fromQuarks(100000)->whole);
        $this->assertEquals(5, Kin::fromQuarks(500000)->whole);
        $this->assertEquals(0, Kin::fromQuarks(10000)->whole);
        $this->assertEquals(0, Kin::fromQuarks(0)->whole);

        $this->assertEquals(0, Kin::fromQuarks(99999)->whole);
        $this->assertEquals(1, Kin::fromQuarks(100001)->whole);
    }

    public function testToQuarks() {
        $this->assertEquals(100000, (new Kin(1))->toQuarks());
        $this->assertEquals(500000, (new Kin(5))->toQuarks());
        $this->assertEquals(0, (new Kin(0))->toQuarks());

        $this->assertEquals(-100000, (new Kin(-1))->toQuarks());
    }

    public function testFromDecimal() {
        $kin = Kin::fromDecimal(1.23456);
        $this->assertEquals(123456, $kin->toQuarks());
    }

    public function testToDecimal() {
        $kin = new Kin(1, 23456);
        $this->assertEquals(1.23456, $kin->toDecimal());
    }

    public function testArithmeticOperations() {
        $kin1 = Kin::fromDecimal(1.23456);
        $kin2 = Kin::fromDecimal(2.34567);

        $sum_kin = $kin1->add($kin2);
        $this->assertEquals(358023, $sum_kin->toQuarks());

        $difference = $kin1->subtract($kin2);
        $this->assertEquals(-111111, $difference->toQuarks());

        $product = $kin1->multiply(2);
        $this->assertEquals(246912, $product->toQuarks());

        $quotient = $kin1->divide(2);
        $this->assertEquals(61728, $quotient->toQuarks());
    }
}