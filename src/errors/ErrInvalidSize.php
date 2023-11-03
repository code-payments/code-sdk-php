<?php
namespace CodeWallet\Errors;

class ErrInvalidSize extends \Exception {
    public function __construct() {
        parent::__construct("invalid size");
    }
}
