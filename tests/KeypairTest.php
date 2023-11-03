<?php
namespace CodeWallet\Tests;

use PHPUnit\Framework\TestCase;
use CodeWallet\Library\Keypair;
use StephenHill\Base58;

class KeypairTest extends TestCase {

    public function testGenerateNewKeypair() {
        $keypair = Keypair::generate();
        $this->assertEquals(32, strlen($keypair->getPrivateValue()));
        $this->assertEquals(32, strlen($keypair->getPublicValue()));
    }

    public function testCreateKeypairFromSecretKey() {
        $base64SecretKey = 'mdqVWeFekT7pqy5T49+tV12jO0m+ESW7ki4zSU9JiCgbL0kJbj5dvQ/PqcDAzZLZqzshVEs01d1KZdmLh4uZIg==';
        $secretKey = base64_decode($base64SecretKey, true);
        $keypair = Keypair::fromSecretKey($secretKey);
        $base58 = new Base58();
        $this->assertEquals('2q7pyhPwAwZ3QMfZrnAbDhnh9mDUqycszcpf86VgQxhF', $base58->encode($keypair->getPublicValue()));

        $secretKeyString = implode(',', array_map('ord', str_split($keypair->getSecretKey())));
        $expectedSecret = '153,218,149,89,225,94,145,62,233,171,46,83,227,223,173,87,93,163,59,73,' .
                          '190,17,37,187,146,46,51,73,79,73,136,40,27,47,73,9,110,62,93,189,15,207,' .
                          '169,192,192,205,146,217,171,59,33,84,75,52,213,221,74,101,217,139,135,139,153,34';
        $this->assertEquals($expectedSecret, $secretKeyString);
    }

    public function testGenerateKeypairFromRandomSeed() {
        $seed = str_repeat(chr(8), 32);
        $keypair = Keypair::fromSeed($seed);
        $base58 = new Base58();
        $this->assertEquals('2KW2XRd9kwqet15Aha2oK3tYvd3nWbTFH1MBiRAv1BE1', $base58->encode($keypair->getPublicValue()));
    }
}
?>