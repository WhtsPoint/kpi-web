<?php

namespace App;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class ContentValidator
{
    public function __construct(
        private readonly Request $request,
        private readonly Response $response
    ) {
    }

    public function validateJsonAndSerialize(): array|false
    {
        if (($this->request->header['content-type'] ?? '') !== 'application/json') {
            $this->response->status(422);
            $this->response->end(json_encode(['error' => 'Invalid content type']));

            return false;
        }

        $content = $this->request->getContent();
        $decoded = is_string($content) ? json_decode($content, true) : null;

        if ($decoded === null) {
            $this->response->status(422);
            $this->response->end(json_encode(['error' => 'Payload is not a json']));

            return false;
        }

        return $decoded;
    }
}