<?php

namespace App\Services;

use App\Models\Admin;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use RuntimeException;
use UnexpectedValueException;

class JwtService
{
    public function issue(Admin $admin): string
    {
        $now = time();

        $payload = [
            'sub' => $admin->id,
            'username' => $admin->username,
            'role' => $admin->role,
            'org_code' => $admin->org_code,
            'iat' => $now,
            'exp' => $now + (config('jwt.ttl') * 60),
        ];

        return JWT::encode($payload, $this->secret(), config('jwt.algo'));
    }

    /**
     * Decode and verify a token, returning the admin id it was issued for.
     *
     * @throws RuntimeException when the token is missing, malformed, expired, or invalid.
     */
    public function subjectId(string $token): string
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret(), config('jwt.algo')));
        } catch (ExpiredException|SignatureInvalidException|UnexpectedValueException $e) {
            throw new RuntimeException('Invalid or expired token.', previous: $e);
        }

        return $decoded->sub;
    }

    private function secret(): string
    {
        $secret = config('jwt.secret');

        if (! $secret) {
            throw new RuntimeException('JWT_SECRET is not configured.');
        }

        return $secret;
    }
}
