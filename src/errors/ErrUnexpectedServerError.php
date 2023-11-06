<?php

namespace CodeWallet\Errors;

class ErrUnexpectedServerError extends \Exception {
    private $error;

    public function __construct($error) {
        $this->error = $error;
        parent::__construct("unexpected server error: {$error}");
    }

    public function getError() {
        return $this->error;
    }
}