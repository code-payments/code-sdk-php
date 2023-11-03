<?php

namespace CodeWallet\Errors;

class ErrUnexpectedError extends \Exception {
    public function __construct() {
        parent::__construct("unexpected error");
    }
}
