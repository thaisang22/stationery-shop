<?php

declare(strict_types=1);

namespace App\Services;

class SePayService
{
    private string $secret;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->secret = $config['sepay']['secret'];

        error_log(
            'SEPAY SECRET LENGTH: ' . strlen($this->secret)
        );

        if ($this->secret === '') {
            throw new \RuntimeException(
                'SEPAY_WEBHOOK_SECRET is not configured.'
            );
        }
    }

    public function verifySignature(
        string $payload,
        string $timestamp,
        string $signature
    ): bool {
        if ($timestamp === '' || $signature === '') {
            return false;
        }

        $expected = 'sha256=' . hash_hmac(
            'sha256',
            $timestamp . '.' . $payload,
            $this->secret
        );

        return hash_equals($expected, $signature);
    }

    public function verifyTimestamp(
        string $timestamp,
        int $maxAge = 300
    ): bool {
        if (!ctype_digit($timestamp)) {
            return false;
        }

        return abs(time() - (int) $timestamp) <= $maxAge;
    }
}