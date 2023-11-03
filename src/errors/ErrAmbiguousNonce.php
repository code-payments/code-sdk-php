<?php
namespace CodeWallet\Errors;

class ErrAmbiguousNonce extends \Exception {
    public function __construct() {
        parent::__construct("cannot derive nonce from both clientSecret and idempotencyKey");
    }
}
