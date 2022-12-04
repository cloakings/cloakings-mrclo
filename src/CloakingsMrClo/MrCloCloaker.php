<?php

namespace Cloakings\CloakingsMrClo;

use Cloakings\CloakingsCommon\CloakerInterface;
use Cloakings\CloakingsCommon\CloakerIpExtractor;
use Cloakings\CloakingsCommon\CloakerIpExtractorModeEnum;
use Cloakings\CloakingsCommon\CloakerResult;
use Cloakings\CloakingsCommon\CloakModeEnum;
use Gupalo\Json\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MrCloCloaker implements CloakerInterface
{
    private string $host = '';

    public function __construct(
        private readonly string $token,
        private MrCloParams $params = new MrCloParams(),
        private readonly MrCloHttpClient $httpClient = new MrCloHttpClient(),
    ) {
    }

    public function handle(Request $request): CloakerResult
    {
        $prevHost = $this->replaceHost($request);

        $apiResponse = $this->httpClient->execute(
            $this->params,
            $this->token,
            $this->getIp($request),
            $this->getData($request),
        );

        $this->restoreHost($prevHost, $request);

        return $this->createResult($apiResponse);
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function setParams(MrCloParams $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function createResult(MrCloApiResponse $apiResponse): CloakerResult
    {
        return new CloakerResult(
            mode: match (true) {
                $apiResponse->isBot => CloakModeEnum::Fake,
                default => CloakModeEnum::Real,
            },
            response: new Response($apiResponse->content),
            apiResponse: $apiResponse,
            params: [
                'mode_button' => $apiResponse->modeButton,
                'target' => $apiResponse->target,
                'mode' => $apiResponse->mode,
            ]
        );
    }

    public function getLastApiResponse(): MrCloApiResponse
    {
        return $this->lastApiResponse ?? MrCloApiResponse::create([]);
    }


    private function getIp(Request $request): string
    {
        return (new CloakerIpExtractor())->getIp($request, CloakerIpExtractorModeEnum::Aggressive);
    }

    private function getData(Request $request): array
    {
        try {
            $sessionData = $request->getSession()->all() ?: [];
        } catch (\Throwable) {
            $sessionData = [];
        }

        return [
            'utm' => Json::toString(array_merge(
                $sessionData,
                $request->query->all(),
            )),
            'query' => Json::toString($request->getQueryString()),
            'headers' => Json::toString($this->getHeaders($request)),
        ];
    }

    private function getHeaders(Request $request): array
    {
        $result = [];

        $items = $request->server->all();
        foreach ($items as $key => $value) {
            $key = mb_strtolower($key);
            if (str_starts_with($key, 'http_')) {
                $result[str_replace('_', '-', mb_substr($key, 5))] = $value;
            }
        }

        return $result;
    }

    private function restoreHost(?string $prevHost, Request $request): void
    {
        if ($prevHost === null) {
            $request->server->remove('http_host');
        } elseif ($prevHost !== '') {
            $request->server->set('http_host', $prevHost);
        }
    }

    private function replaceHost(Request $request): ?string
    {
        $prevHost = '';
        if ($this->host !== '') {
            $prevHost = $request->server->get('http_host', null);
            $request->server->set('http_host', $this->host);
        }

        return $prevHost;
    }
}
