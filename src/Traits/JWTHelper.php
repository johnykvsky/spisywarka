<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait JWTHelper
{
    /**
     * @param Request $request
     * @return string
     */
    public function getToken(Request $request): string
    {
        if (empty($request->headers->get('Authorization'))) {
            throw new \InvalidArgumentException('No valid JWT token');
        }
        return str_replace('Bearer ', '', $request->headers->get('Authorization'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getTokenPayload(Request $request): array
    {
        $token = $this->getToken($request);
        if ($token) {
            $payload = \explode('.', $token)[1];
            $decodedPayload = \base64_decode($payload);
            return \json_decode($decodedPayload, true);
        }

        return [];
    }
}
