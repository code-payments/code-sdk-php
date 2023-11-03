<?php

namespace CodeWallet\Library;

class Kin {
    public const DECIMALS = 5;
    public const QUARKS_PER_KIN = 10 ** self::DECIMALS;
    public const MINT_ADDRESS = "kinXdEcpDQeHPEuQnqmUgtYykqKGVFq6CeVX5iAHJq6";
    
    public $whole;
    public $quarks;

    public static $mint; 
    public static $zero;
    public static $one;
    public static $maxValue;
    public static $minValue;

    public function __construct($whole, $quarks=0) {
        $this->whole = $whole;
        $this->quarks = $quarks;
        $this->_normalize();
    }

    private function _normalize() {
        $this->whole += intdiv($this->quarks, self::QUARKS_PER_KIN);
        $this->quarks %= self::QUARKS_PER_KIN;
    }

    public function toQuarks() {
        return $this->whole * self::QUARKS_PER_KIN + $this->quarks;
    }

    public function toDecimal() {
        return $this->whole + $this->quarks / self::QUARKS_PER_KIN;
    }

    public static function fromQuarks($quarks) {
        $whole = intdiv($quarks, self::QUARKS_PER_KIN);
        $remainingQuarks = $quarks % self::QUARKS_PER_KIN;
        return new Kin($whole, $remainingQuarks);
    }

    public static function fromDecimal($decimalValue) {
        $quarks = round($decimalValue * self::QUARKS_PER_KIN);
        return self::fromQuarks($quarks);
    }

    public function add(Kin $other) {
        $resultQuarks = $this->toQuarks() + $other->toQuarks();
        return Kin::fromQuarks($resultQuarks);
    }

    public function subtract(Kin $other) {
        $resultQuarks = $this->toQuarks() - $other->toQuarks();
        return Kin::fromQuarks($resultQuarks);
    }

    public function multiply($factor) {
        $resultQuarks = $this->toQuarks() * $factor;
        return Kin::fromQuarks($resultQuarks);
    }

    public function divide($divisor) {
        $resultQuarks = intdiv($this->toQuarks(), $divisor);
        return Kin::fromQuarks($resultQuarks);
    }
}

// Initializing class-level constants
Kin::$zero = new Kin(0, 0);
Kin::$one = new Kin(1, 0);
Kin::$maxValue = new Kin(9223372036854775807, Kin::QUARKS_PER_KIN - 1); // Max for a 64-bit integer
Kin::$minValue = new Kin(-9223372036854775808, 0); // Min for a 64-bit integer

// Initialization for PublicKey
Kin::$mint = PublicKey::fromBase58(Kin::MINT_ADDRESS);
