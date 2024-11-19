<?php

namespace App;

use Exception;

class JWT
{
    public function __construct(
        private string $secret
    ) {
    }

    public function createToken(array $payload): string
    {
        $base64UrlHeader = $this->base64UrlEncode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        $base64UrlSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($base64UrlSignature);

        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    public function isValidToken($token): bool
    {
        [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = explode('.', $token);

        $signature = $this->base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret, true);

        return hash_equals($signature, $expectedSignature);
    }

    /**
     * @throws InvalidTokenException
     */
    public function decodeToken(string $token): array
    {
        if ($this->isValidToken($token) === false) {
            throw new InvalidTokenException('Token is not valid');
        }

        [, $base64UrlPayload,] = explode('.', $token);
        $payload = json_decode($this->base64UrlDecode($base64UrlPayload), true);

        if (is_int($payload['exp'] ?? null) && $payload['exp'] < time()) {
            throw new InvalidTokenException('Token expired');
        }

        return $payload;
    }

    private function base64UrlEncode($data): string
    {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');
        return rtrim($base64Url, '=');
    }

    private function base64UrlDecode($data): string
    {
        $base64 = strtr($data, '-_', '+/');
        $base64Padded = str_pad($base64, strlen($base64) % 4, '=');
        return base64_decode($base64Padded);
    }
}