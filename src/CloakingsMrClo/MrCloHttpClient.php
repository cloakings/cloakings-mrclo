<?php

namespace Cloakings\CloakingsMrClo;

use Gupalo\Json\Json;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class MrCloHttpClient
{
    private ?MrCloApiResponse $lastApiResponse = null;

    private const SERVICE_NAME = 'mrclo';

    public function __construct(
        private readonly string $apiUrl = 'https://gate.mr-clo.com/api/v2/handler',
        private readonly HttpClientInterface $httpClient = new CurlHttpClient(),
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function execute(
        MrCloParams $params,
        string $token,
        string $ip,
        array $data,
    ): MrCloApiResponse
    {
        try {
            $startTime = microtime(true);
            $response = $this->httpClient->request(Request::METHOD_POST, $this->apiUrl, [
                'headers' => [
                    'MrClo-token' => $token,
                    'MrClo-client-ip' => $ip,
                    'MrClo-revise' => $params->source->value,
                    'MrClo-mode-button' => $params->modeButton,
                    'MrClo-referer-check' => $params->refererCheck,
                    'MrClo-utm-check' => $params->utmCheck,
                    'MrClo-vpn-check' => $params->vpnCheck,
                    'MrClo-country-disable' => implode(',', $params->fakeCountries),
                    'MrClo-block-ios' => $params->blockIos,
                ],
                'body' => http_build_query($data),
                'verify_peer' => false,
                'verify_host' => false,
                'max_duration' => 4000, // ms
            ]);
            $time = microtime(true) - $startTime;

            $status = $response->getStatusCode();
            $headers = $response->getHeaders();
            $content = $response->getContent();
            $data = array_merge(
                Json::toArray(trim($content, " \t\n\r\0\x0B\"")),
                [
                    'response_status' => $status,
                    'response_headers' => $headers,
                    'response_body' => $content,
                    'response_time' => $time,
                ],
            );
        } catch (Throwable $e) {
            $this->logger->error('cloaking_request_error', ['service' => self::SERVICE_NAME, 'params' => $params, 'status' => $status ?? 0, 'headers' => $headers ?? [], 'content' => $content ?? '', 'exception' => $e]);

            return MrCloApiResponse::create([]);
        }

        $this->logger->info('cloaking_request', ['service' => self::SERVICE_NAME, 'params' => $params, 'status' => $status ?? 0, 'headers' => $headers ?? [], 'content' => $content ?? '', 'time' => $time ?? 0]);

        return MrCloApiResponse::create($data ?? []);
    }
}
