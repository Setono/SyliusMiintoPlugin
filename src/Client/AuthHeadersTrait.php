<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Client;

use Exception;
use Psr\Http\Message\RequestInterface;

trait AuthHeadersTrait
{
    /**
     * @throws Exception
     */
    public function addAuthHeaders(RequestInterface $request, string $channelId, string $token, string $authType = 'MNT-HMAC-SHA256-1-0'): RequestInterface
    {
        $requestStr = $request->getMethod() . "\n"
            . $request->getUri()->getHost() . "\n"
            . $request->getUri()->getPath() . "\n"
            . $request->getUri()->getQuery()
        ;

        $timestamp = time();
        $seed = random_int(0, 100);

        $resourceSignature = hash('sha256', $requestStr);
        $headerSignature = hash('sha256', $channelId . "\n" . $timestamp . "\n" . $seed . "\n" . $authType);
        $payloadSignature = hash('sha256', (string) $request->getBody());

        $signature = hash_hmac('sha256', $resourceSignature . "\n" . $headerSignature . "\n" . $payloadSignature, $token);

        return $request
            ->withHeader('Miinto-Api-Auth-ID', $this->channelId)
            ->withHeader('Miinto-Api-Auth-Signature', $signature)
            ->withHeader('Miinto-Api-Auth-Timestamp', (string) $timestamp)
            ->withHeader('Miinto-Api-Auth-Seed', (string) $seed)
            ->withHeader('Miinto-Api-Auth-Type', $authType)
            ->withHeader('Miinto-Api-Control-Flavour', 'Miinto-Generic')
            ->withHeader('Miinto-Api-Control-Version', '2.2')
        ;
    }
}
