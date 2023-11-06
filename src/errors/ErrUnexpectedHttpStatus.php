<?php

namespace CodeWallet\Errors;

class ErrUnexpectedHttpStatus extends \Exception {
    private $status;
    private $message;

    public function __construct($status, $message) {
        $this->status = $status;
        $this->message = $message;
        parent::__construct("unexpected HTTP status: {$status}, {$message}");
    }

    public function getStatus() {
        return $this->status;
    }

    public function getCustomMessage() {
        return $this->message;
    }
}
