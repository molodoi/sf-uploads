<?php

namespace App\Service;

class JWTService
{
    /**
     * Generate JWT.
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if ($validity > 0) {
            $now = new \DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // Encode base64
        $base64Header = base64_encode((string) json_encode($header));
        $base64Payload = base64_encode((string) json_encode($payload));

        // "Clean" encoded values (remove +, / and =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // Generate signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header.'.'.$base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // Created token
        $jwt = $base64Header.'.'.$base64Payload.'.'.$base64Signature;

        return $jwt;
    }

    // Check if token is valid (properly trained)

    public function isValid(string $token): bool
    {
        return 1 === preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        );
    }

    // Get Payload
    public function getPayload(string $token): array
    {
        // Explode token
        $array = explode('.', $token);

        // Decode Payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // Get Header
    public function getHeader(string $token): array
    {
        // Explode token
        $array = explode('.', $token);

        // Decode Header
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // Check if token has expired
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new \DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // Check token signature
    public function check(string $token, string $secret): bool
    {
        // Get header and payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Regenerate token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}
