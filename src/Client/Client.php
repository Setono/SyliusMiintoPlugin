<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Client;

use Exception;
use InvalidArgumentException;
use const PHP_QUERY_RFC3986;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusMiintoPlugin\Exception\AuthenticationFailedException;
use Setono\SyliusMiintoPlugin\Exception\RequestFailedException;
use Setono\SyliusMiintoPlugin\Position\Positions;

final class Client implements ClientInterface
{
    use AuthHeadersTrait;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var string */
    private $authEndpoint;

    /** @var string */
    private $resourceEndpoint;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var bool */
    private $authed = false;

    /** @var array */
    private $shopIds = [];

    /** @var string */
    private $channelId;

    /** @var string */
    private $token;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $authEndpoint,
        string $resourceEndpoint,
        string $username,
        string $password
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->authEndpoint = $authEndpoint;
        $this->resourceEndpoint = $resourceEndpoint;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function getShopIds(): array
    {
        if (false === $this->authed) {
            $this->auth();
        }

        return $this->shopIds;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function getShopDetails(string $shopId): array
    {
        $url = sprintf('%s/shops/%s', $this->resourceEndpoint, $shopId);

        $response = $this->sendRequest('GET', $url);

        return $response['data'];
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws StringsException
     * @throws JsonException
     */
    public function getOrder(string $shopId, int $orderId): array
    {
        $url = sprintf('%s/shops/%s/orders/%d', $this->resourceEndpoint, $shopId, $orderId);

        $response = $this->sendRequest('GET', $url);

        return $response['data'];
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function updateOrder(string $shopId, int $orderId, array $acceptedPositions = [], array $declinedPositions = []): void
    {
        $body = [];

        if (count($acceptedPositions) > 0) {
            $body['acceptedPositions'] = $acceptedPositions;
        }

        if (count($declinedPositions) > 0) {
            $body['declinedPositions'] = $declinedPositions;
        }

        if (count($body) === 0) {
            throw new InvalidArgumentException('No accepted or declined positions');
        }

        $this->sendRequest('PATCH', sprintf('%s/shops/%s/orders/%d', $this->resourceEndpoint, $shopId, $orderId), $body);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function getTransfers(string $shopId, array $options = []): array
    {
        $query = http_build_query($options, '', '&', PHP_QUERY_RFC3986);
        $url = sprintf('%s/shops/%s/transfers?%s', $this->resourceEndpoint, $shopId, $query);

        $response = $this->sendRequest('GET', $url);

        return $response['data'];
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function updateTransfer(string $shopId, int $transferId, Positions $positions): ?int
    {
        if (!$positions->hasPositions()) {
            throw new InvalidArgumentException('No accepted or declined positions');
        }

        $body = [];

        if ($positions->hasAccepted()) {
            $body['acceptedPositions'] = [];
            foreach ($positions->getAccepted() as $id) {
                $body['acceptedPositions'][] = ['id' => $id];
            }
        }

        if ($positions->hasDeclined()) {
            $body['declinedPositions'] = [];
            foreach ($positions->getDeclined() as $id) {
                $body['declinedPositions'][] = ['id' => $id];
            }
        }

        $response = $this->sendRequest('PATCH', sprintf('%s/shops/%s/transfers/%d', $this->resourceEndpoint, $shopId, $transferId), $body);

        return $response['data']['newOrder']['id'] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function getShippingProviders(string $shopId, int $orderId): array
    {
        $response = $this->sendRequest('GET', sprintf('%s/shops/%s/shipping-providers/orders/%d', $this->resourceEndpoint, $shopId, $orderId));

        return $response['data'];
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     * @throws Exception
     * @throws RequestFailedException
     */
    private function sendRequest(string $method, string $url, array $body = null): array
    {
        if (false === $this->authed) {
            $this->auth();
        }

        $request = $this->createRequest($method, $url, $body);

        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new RequestFailedException($request, $response, $response->getStatusCode());
        }

        return \Safe\json_decode((string) $response->getBody(), true);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function createRequest(string $method, string $url, array $body = null): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $url);
        $request = $request
            ->withHeader('Content-Type', 'application/json')
        ;

        if (null !== $body) {
            $request = $request->withBody($this->streamFactory->createStream(\Safe\json_encode($body)));
        }

        return $this->addAuthHeaders($request, $this->channelId, $this->token);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws StringsException
     * @throws JsonException
     */
    private function auth(): void
    {
        $body = $this->streamFactory->createStream(\Safe\json_encode([
            'identifier' => $this->username,
            'secret' => $this->password,
        ]));

        $request = $this->requestFactory->createRequest('POST', sprintf('%s/channels', $this->authEndpoint));
        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body)
        ;

        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new AuthenticationFailedException($this->username, $this->password);
        }

        $content = \Safe\json_decode((string) $response->getBody(), true);

        if ('success' !== $content['meta']['status']) {
            throw new AuthenticationFailedException($this->username, $this->password, $content['meta']['status']);
        }

        $this->shopIds = array_keys($content['data']['privileges']['__GLOBAL__']['Store']);
        $this->channelId = $content['data']['id'];
        $this->token = $content['data']['token'];

        $this->authed = true;
    }
}
