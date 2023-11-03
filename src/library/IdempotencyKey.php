<?php

namespace CodeWallet\Library;

use StephenHill\Base58;

use CodeWallet\Errors\ErrInvalidSize;

class IdempotencyKey {
    const MAX_LENGTH = 11;
    private $value;

    public function __construct($data = null) {
        if ($data === null) {
            $data = str_repeat(chr(0), self::MAX_LENGTH);
        }

        if (strlen($data) !== self::MAX_LENGTH) {
            throw new ErrInvalidSize();
        }

        $this->value = $data;
    }

    public function getRawValue() {
        return $this->value;
    }

    public static function fromClientSecret($data) {
        $base58 = new Base58();
        return new self($base58->decode($data));
    }

    public static function fromSeed($seed) {
        // Not ideal, an 11-byte hashing function is needed, and no such function exists
        $hashedSeed = hash('sha256', $seed, true);
        return new self(substr($hashedSeed, 0, self::MAX_LENGTH));
    }

    public static function generate() {
        $keypair = Keypair::generate();
        $seed = substr($keypair->getPrivateValue(), 0, self::MAX_LENGTH);
        return new self($seed);
    }

    public function __toString() {
        $base58 = new Base58();
        return $base58->encode($this->value);
    }
}

?>