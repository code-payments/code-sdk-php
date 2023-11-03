<?php
namespace CodeWallet\Errors;

class ErrInvalidCurrency extends \Exception {
    public function __construct() {
        parent::__construct("invalid currency");
    }
}
