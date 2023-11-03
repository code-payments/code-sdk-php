<?php
namespace CodeWallet\Library;

use ParagonIE_Sodium_Compat;

class Keypair {
    private $privateKey;
    private $publicKey;

    public function __construct($privateKey, $publicKey) {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }

    public static function generate() {
        $keypair = ParagonIE_Sodium_Compat::crypto_sign_keypair();
        $secretKey = ParagonIE_Sodium_Compat::crypto_sign_secretkey($keypair);
        $publicKey = ParagonIE_Sodium_Compat::crypto_sign_publickey($keypair);

        // Extract just the 32-byte private key portion from the secretKey.
        $privateKey = substr($secretKey, 0, 32);

        return new self($privateKey, $publicKey);
    }

    public static function fromSecretKey($secretKey) {
        $privateKey = substr($secretKey, 0, 32);
        $publicKey = ParagonIE_Sodium_Compat::crypto_sign_publickey_from_secretkey($secretKey); // Use the full 64-byte secret key
        return new self($privateKey, $publicKey);
    }

    public static function fromSeed($seed) {
        $keypair = ParagonIE_Sodium_Compat::crypto_sign_seed_keypair($seed);
        $secretKey = ParagonIE_Sodium_Compat::crypto_sign_secretkey($keypair);
        $publicKey = ParagonIE_Sodium_Compat::crypto_sign_publickey($keypair);

        // Extract just the 32-byte private key portion from the secretKey.
        $privateKey = substr($secretKey, 0, 32);

        return new self($privateKey, $publicKey);
    }

    public function getPublicKey() {
        return new PublicKey($this->publicKey);
    }

    public function getPublicValue() {
        return $this->publicKey;
    }

    public function getPrivateValue() {
        return $this->privateKey;
    }

    public function getSecretKey() {
        return $this->privateKey . $this->publicKey;
    }

    public function sign($message) {
        return ParagonIE_Sodium_Compat::crypto_sign_detached($message, $this->privateKey . $this->publicKey);
    }

    public function verify($message, $signature) {
        return ParagonIE_Sodium_Compat::crypto_sign_verify_detached($signature, $message, $this->publicKey);
    }
}

?>