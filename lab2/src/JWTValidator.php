<?php

namespace App;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class JWTValidator
{
    public function __construct(
        private readonly JWT $jwt
    ) {
    }

    public function validateTokenAndSerialize(Request $request, Response $response): ?array
    {
        $token = $request->header['authorization'] ?? null;

        if (is_string($token) === false) {
            $response->status(401);
            $response->end(json_encode(['error' => 'Token is not provided']));

            return null;
        }

        try {
            return $this->jwt->decodeToken($token);
        } catch (InvalidTokenException $exception) {
            $response->status(401);
            $response->end(json_encode(['error' => $exception->getMessage()]));

            return null;
        }
    }
}