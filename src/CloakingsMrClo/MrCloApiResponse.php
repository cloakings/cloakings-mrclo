<?php

namespace Cloakings\CloakingsMrClo;

class MrCloApiResponse
{
    public function __construct(
        public readonly bool $isBot = false,
        public readonly bool $modeButton = false,
        public readonly string $target = '',
        public readonly string $mode = '',
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
            target: (string)($a['target'] ?? ''),
            mode: (string)($a['target_settings'] ?? ''),
            content: (string)($a['content'] ?? ''),
            responseStatus: (int)($a['response_status'] ?? 0),
            responseHeaders: ($a['response_headers'] ?? []),
            responseBody: ($a['response_body'] ?? ''),
            responseTime: ($a['response_time'] ?? 0.0),
        );
    }
}
