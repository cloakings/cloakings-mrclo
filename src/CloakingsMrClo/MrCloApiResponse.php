<?php

namespace Cloakings\CloakingsMrClo;

use Cloakings\CloakingsCommon\CloakerApiResponseInterface;
use Cloakings\CloakingsCommon\CloakerHelper;

class MrCloApiResponse implements CloakerApiResponseInterface
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

    public function isReal(): bool
    {
        return !$this->isFake();
    }

    public function isFake(): bool
    {
        return $this->isBot;
    }

    public function getResponseStatus(): int
    {
        return $this->responseStatus;
    }

    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getResponseTime(): float
    {
        return $this->responseTime;
    }

    public function jsonSerialize(): array
    {
        return [
            'is_bot' => $this->isBot,
            'mode_button' => $this->modeButton,
            'target' => $this->target,
            'mode' => $this->mode,
            'content' => $this->content,
            'response_status' => $this->responseStatus,
            'response_headers' => CloakerHelper::flattenHeaders($this->responseHeaders),
            'response_body' => $this->responseBody,
            'response_time' => $this->responseTime,
        ];
    }
}
