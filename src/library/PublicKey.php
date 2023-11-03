<?php
namespace CodeWallet\Library;

use StephenHill\Base58;

class PublicKey {
    private $publicKey;

    public function __construct($publicKey) {
        $this->publicKey = $publicKey;
    }

    public static function fromBase58($base58Encoded) {
        $base58 = new Base58();
        return new self($base58->decode($base58Encoded));
    }

    public function toBytes() {
        return $this->publicKey;
    }

    public function toBase58() {
        $base58 = new Base58();
        return $base58->encode($this->publicKey);
    }

    public function __toString() {
        return $this->toBase58();
    }
}

?>