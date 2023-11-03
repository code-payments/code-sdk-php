<?php
namespace CodeWallet\Errors;

class ErrInvalidMode extends \Exception {
    public function __construct() {
        parent::__construct("invalid mode");
    }
}
