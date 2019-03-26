<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Safe\Exceptions\StringsException;

final class RequestFailedException extends \RuntimeException
{
    private $request;
    private $response;
    private $statusCode;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param int $statusCode
     *
     * @throws StringsException
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, int $statusCode)
    {
        $this->request = $request;
        $this->response = $response;
        $this->statusCode = $statusCode;

        parent::__construct(\Safe\sprintf('Request failed with status code %d', $this->statusCode));
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
