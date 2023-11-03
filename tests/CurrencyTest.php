<?php

use PHPUnit\Framework\TestCase;

use CodeWallet\Errors\ErrInvalidCurrency;
use CodeWallet\Library\Currency;

class CurrencyTest extends TestCase {

    public function testCurrencyCodeToIndex() {
        $this->assertEquals(0, Currency::currencyCodeToIndex("kin"));
        $this->assertEquals(140, Currency::currencyCodeToIndex("usd"));
        $this->assertEquals(43, Currency::currencyCodeToIndex("eur"));

        $this->expectException(ErrInvalidCurrency::class);
        Currency::currencyCodeToIndex("invalid");
    }

    public function testIndexToCurrencyCode() {
        $this->assertEquals("kin", Currency::indexToCurrencyCode(0));
        $this->assertEquals("usd", Currency::indexToCurrencyCode(140));
        $this->assertEquals("eur", Currency::indexToCurrencyCode(43));

        $this->expectException(ErrInvalidCurrency::class);
        Currency::indexToCurrencyCode(-1);

        $this->expectException(ErrInvalidCurrency::class);
        Currency::indexToCurrencyCode(200);  // 200 is out of bounds
    }

    public function testIsValidCurrency() {
        $this->assertTrue(Currency::isValidCurrency("kin"));
        $this->assertTrue(Currency::isValidCurrency("usd"));
        $this->assertTrue(Currency::isValidCurrency("eur"));

        $this->assertFalse(Currency::isValidCurrency("invalid"));
    }
}

?>
