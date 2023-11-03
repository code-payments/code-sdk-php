<?php
namespace CodeWallet\Errors;

class ErrCurrencyRequired extends \Exception {
    public function __construct() {
        parent::__construct("currency is required");
    }
}
