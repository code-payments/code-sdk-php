<?php

namespace CodeWallet\Client;

use CodeWallet\Errors\ErrUnexpectedError;
use CodeWallet\Errors\ErrUnexpectedHttpStatus;
use CodeWallet\Errors\ErrUnexpectedServerError;

class Connection
{
    private string $endpoint;

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function get(string $method, array $params): array
    {
        $url = "{$this->endpoint}/{$method}";
        $response = file_get_contents($url . '?' . http_build_query($params), false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Content-Type: application/json\r\n",
            ],
        ]));

        if ($response === false) {
            throw new ErrUnexpectedError();
        }

        $httpCode = $this->getHttpStatusCode($http_response_header);
        if ($httpCode != 200) {
            throw new ErrUnexpectedHttpStatus($httpCode, $response);
        }

        $json_data = json_decode($response, true);
        if (isset($json_data['error'])) {
            throw new ErrUnexpectedServerError($json_data['error']);
        }

        if (isset($json_data['success']) && $json_data['success']) {
            return $json_data;
        }

        throw new ErrUnexpectedError();
    }

    public function post(string $method, array $body): bool
    {
        $url = "{$this->endpoint}/{$method}";
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($body),
            ],
        ]));

        if ($response === false) {
            throw new ErrUnexpectedError();
        }

        $httpCode = $this->getHttpStatusCode($http_response_header);
        if ($httpCode != 200) {
            throw new ErrUnexpectedHttpStatus($httpCode, $response);
        }

        $json_data = json_decode($response, true);
        if (isset($json_data['error'])) {
            throw new ErrUnexpectedServerError($json_data['error']);
        }

        if (isset($json_data['success']) && $json_data['success']) {
            return true;
        }

        throw new ErrUnexpectedError();
    }

    private function getHttpStatusCode(array $http_response_header): int
    {
        if (!is_array($http_response_header) || empty($http_response_header)) {
            return 0;
        }
        $status_line = $http_response_header[0];
        preg_match('{HTTP/\S*\s(\d{3})}', $status_line, $match);
        return $match[1] ?? 0;
    }
}