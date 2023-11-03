<?php
namespace CodeWallet\Errors;

class ErrAmountRequired extends \Exception {
    public function __construct() {
        parent::__construct("amount is required");
    }
}
