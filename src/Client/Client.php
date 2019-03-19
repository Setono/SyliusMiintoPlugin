<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Setono\SyliusMiintoPlugin\Exception\AuthenticationFailedException;
use Setono\SyliusMiintoPlugin\Exception\RequestFailedException;

final class Client implements ClientInterface
{
    use AuthHeadersTrait;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $authed = false;

    /**
     * @var array
     */
    private $shopIds = [];

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var string
     */
    private $token;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $username,
        string $password
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @inheritdoc
     * @throws ClientExceptionInterface
     */
    public function getShopIds(): array
    {
        if(false === $this->authed) {
            $this->auth();
        }

        return $this->shopIds;
    }

    /**
     * @inheritdoc
     * @throws ClientExceptionInterface
     */
    public function getShopDetails(string $shopId): array
    {
        $url = sprintf('https://api-order.miinto.net/shops/%s', $shopId);

        return $this->sendRequest('GET', $url);
    }

    /**
     * @inheritdoc
     * @throws ClientExceptionInterface
     */
    public function getOrders(string $shopId, array $options = []): array
    {
        $query = http_build_query($options, '', '&', PHP_QUERY_RFC3986);
        $url = sprintf('https://api-order.miinto.net/shops/%s/orders?%s', $shopId, $query);

        return $this->sendRequest('GET', $url);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array|null $body
     * @return array
     * @throws ClientExceptionInterface
     * @throws RequestFailedException
     */
    private function sendRequest(string $method, string $url, array $body = null): array
    {
        if(false === $this->authed) {
            $this->auth();
        }

        $request = $this->requestFactory->createRequest($method, $url);
        $request = $request
            ->withHeader('Content-Type', 'application/json')
        ;

        if(null !== $body) {
            $request->withBody($this->streamFactory->createStream(json_encode($body)));
        }

        $request = $this->addAuthHeaders($request, $this->channelId, $this->token);

        $response = $this->httpClient->sendRequest($request);

        if($response->getStatusCode() !== 200) {
            throw new RequestFailedException($request, $response, $response->getStatusCode());
        }

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws AuthenticationFailedException
     */
    private function auth(): void
    {
        $body = $this->streamFactory->createStream(json_encode([
            'identifier' => $this->username,
            'secret' => $this->password
        ]));

        $request = $this->requestFactory->createRequest('POST', 'https://api-auth.miinto.net/channels');
        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body)
        ;

        $response = $this->httpClient->sendRequest($request);

        if($response->getStatusCode() !== 200) {
            throw new AuthenticationFailedException($this->username, $this->password);
        }

        $content = json_decode((string) $response->getBody(), true);

        if('success' !== $content['meta']['status']) {
            throw new AuthenticationFailedException($this->username, $this->password, $content['meta']['status']);
        }

        $this->shopIds = array_keys($content['data']['privileges']['__GLOBAL__']['Store']);
        $this->channelId = $content['data']['id'];
        $this->token = $content['data']['token'];

        $this->authed = true;
    }
}
