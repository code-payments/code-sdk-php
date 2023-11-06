<?php

namespace CodeWallet\Client;

use CodeWallet\Library\PaymentRequestIntent;
use StephenHill\Base58;

class PaymentIntents {

    private static $api = 'https://api.getcode.com/v1';

    public static function create(array $obj): array {
        $client = new Connection(self::$api);
        $base58 = new Base58();
        $obj['mode'] = 'payment';

        $intent = new PaymentRequestIntent($obj);

        $envelope = $intent->sign();
        $body = [
            'intent' => $envelope['intent'],
            'message' => rtrim(strtr(base64_encode($envelope['message']), '+/', '-_'), '='),
            'signature' => $base58->encode($envelope['signature']),
            'webhook' => $obj['webhook']['url'] ?? null
        ];

        $client->post('createIntent', $body);

        return [
            'clientSecret' => $intent->get_client_secret(),
            'id' => $intent->get_intent_id()
        ];
    }

    public static function getStatus(string $intentId): array {
        $client = new Connection(self::$api);
        
        $res = $client->get('getStatus', ['intent' => $intentId]);

        if ($res['status'] === 'SUBMITTED') {
            return ['status' => PaymentIntentState::CONFIRMED];
        }

        return ['status' => PaymentIntentState::PENDING];
    }
}