<?php

namespace CodeWallet\Library;

use CodeWallet\Errors\ErrAmountRequired;
use CodeWallet\Errors\ErrCurrencyRequired;
use CodeWallet\Errors\ErrDestinationRequired;
use CodeWallet\Messages\ExchangeData;
use CodeWallet\Messages\ExchangeDataWithoutRate;
use CodeWallet\Messages\RequestToReceiveBill;
use CodeWallet\Messages\SolanaAccountId;
use CodeWallet\Messages\Message;

class PaymentRequestIntent {

    public $options;
    public $nonce;
    public $convertedAmount;
    public $rendezvousPayload;
    public $rendezvousKeypair;

    public function __construct(array $opt) {
        $this->options = array_merge($opt, ['currency' => isset($opt['currency']) ? strtolower($opt['currency']) : null]);
        $this->validate();

        if (isset($this->options['idempotencyKey'])) {
            $this->nonce = IdempotencyKey::fromSeed($this->options['idempotencyKey']);
        } elseif (isset($this->options['clientSecret'])) {
            $this->nonce = IdempotencyKey::fromClientSecret($this->options['clientSecret']);
        } else {
            $this->nonce = IdempotencyKey::generate();
        }

        $this->options['amount'] = round(floatval($this->options['amount']), 2);
        $this->convertedAmount = intval(round($this->options['amount'] * 100));

        $kind = CodeKind::REQUEST_PAYMENT;
        $amount = $this->convertedAmount;
        $nonce = $this->nonce->getRawValue();

        $this->rendezvousPayload = new CodePayload($kind, $amount, $nonce, $this->options['currency']);
        $this->rendezvousKeypair = Rendezvous::generate_rendezvous_keypair($this->rendezvousPayload);
    }

    private function validate() {
        if (!isset($this->options['destination'])) {
            throw new ErrDestinationRequired();
        }

        if (!isset($this->options['amount'])) {
            throw new ErrAmountRequired();
        }

        if (!isset($this->options['currency'])) {
            throw new ErrCurrencyRequired();
        }
    }

    public function to_msg() {
        $destination = PublicKey::fromBase58($this->options['destination']);
        $currency = $this->options['currency'];
        $amount = $this->options['amount'];

        if ($currency === "kin") {
            $exchangeData = new ExchangeData('kin', 1, Kin::fromDecimal($amount)->toQuarks(), $amount);
        } else {
            $exchangeData = new ExchangeDataWithoutRate($currency, $amount);
        }

        $requestorAccount = new SolanaAccountId($destination->toBytes());
        return new RequestToReceiveBill($requestorAccount, $exchangeData);
    }

    public function to_proto() {
        return (new Message($this->to_msg()))->serialize();
    }

    public function sign() {
        $msg = $this->to_msg();
        $envelope = $this->to_proto();

        $sig = $this->rendezvousKeypair->sign($envelope);
        $intent = $this->rendezvousKeypair->getPublicKey()->toBase58();
        $message = $msg->serialize();
        $signature = $sig;

        return [
            'message' => $message,
            'intent' => $intent,
            'signature' => $signature
        ];
    }

    public function get_client_secret(): string {
        return (string)$this->nonce;
    }

    public function get_intent_id(): string {
        return $this->rendezvousKeypair->getPublicKey()->toBase58();
    }
}

?>