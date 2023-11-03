<?php

namespace CodeWallet\Library;

use CodeWallet\Errors\ErrInvalidSize;
use CodeWallet\Errors\ErrInvalidCurrency;

// CodePayload class represents the payload format for scan codes.
// It handles conversion to and from binary format and validation.
class CodePayload {
    const MAX_LENGTH = 20;

    private $kind;
    private $amount;
    private $nonce;
    private $currency;

    public function __construct(int $kind, int $amount, string $nonce, ?string $currency = null) {
        $this->kind = $kind;
        $this->amount = $amount;
        $this->nonce = $nonce;

        // Validation for currency code
        if ($currency && !Currency::isValidCurrency($currency)) {
            throw new ErrInvalidCurrency();
        }
        $this->currency = $currency;
    }

    public function getKind(): int {
        return $this->kind;
    }

    public function getAmount(): int {
        return $this->amount;
    }

    public function getNonce(): string {
        return $this->nonce;
    }

    public function getCurrency(): ?string {
        return $this->currency;
    }

    public function toBinary(): string {
        $data = str_repeat("\0", 20); // Initialize with 20 null bytes
        $data[0] = chr($this->kind);

        if ($this->kind === CodeKind::REQUEST_PAYMENT) {
            if (!$this->currency) {
                throw new ErrInvalidCurrency();
            }
            
            $currencyIndex = Currency::currencyCodeToIndex($this->currency);
            $data[1] = chr($currencyIndex);
            for ($i = 0; $i < 7; $i++) {
                $data[$i + 2] = chr(($this->amount >> (8 * $i)) & 0xFF);
            }
        } else {
            for ($i = 0; $i < 8; $i++) {
                $data[$i + 1] = chr(($this->amount >> (8 * $i)) & 0xFF);
            }
        }

        $data = substr($data, 0, 9) . $this->nonce;
        
        return $data;
    }

    public static function fromData(string $data): CodePayload {
        if (strlen($data) !== self::MAX_LENGTH) {
            throw new ErrInvalidSize();
        }

        $type = ord($data[0]);
        $amount = 0;
        $nonce = substr($data, 9);
        $currency = null;

        if ($type === CodeKind::REQUEST_PAYMENT) {
            $currencyIndex = ord($data[1]);
            $currency = Currency::indexToCurrencyCode($currencyIndex);
            for ($i = 0; $i < 7; $i++) {
                $amount += ord($data[$i + 2]) << (8 * $i);
            }
        } else {
            for ($i = 0; $i < 8; $i++) {
                $amount += ord($data[$i + 1]) << (8 * $i);
            }
        }
        
        return new CodePayload($type, $amount, $nonce, $currency);
    }
}