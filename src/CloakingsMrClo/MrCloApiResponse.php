<?php

namespace Cloakings\CloakingsMrClo;

class MrCloApiResponse
{
    public function __construct(
        public readonly bool $isBot = false,
        public readonly bool $modeButton = false,
        public readonly string $content = '',
        public readonly int $responseStatus = 0,
        public readonly array $responseHeaders = [],
        public readonly string $responseBody = '',
        public readonly float $responseTime = 0.0,
    ) {
    }

    public static function create(array $a): self
    {
        return new self(
            isBot: !($a['status'] ?? true),
            modeButton: (bool)($a['mode_button'] ?? false),
            content: (string)($apiResponse['content'] ?? ''),
            responseStatus: (int)($apiResponse['response_status'] ?? 0),
            responseHeaders: ($apiResponse['response_headers'] ?? []),
            responseBody: ($apiResponse['response_body'] ?? ''),
            responseTime: ($apiResponse['response_time'] ?? 0.0),
        );
    }
}
