<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RequestFailedException extends \RuntimeException
{
    private $request;
    private $response;
    private $statusCode;

    public function __construct(RequestInterface $request, ResponseInterface $response, int $statusCode)
    {
        $this->request = $request;
        $this->response = $response;
        $this->statusCode = $statusCode;

        parent::__construct(sprintf('Request failed with status code %d', $this->statusCode));
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
