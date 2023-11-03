<?php
namespace CodeWallet\Errors;

class ErrDestinationRequired extends \Exception {
    public function __construct() {
        parent::__construct("destination is required");
    }
}
