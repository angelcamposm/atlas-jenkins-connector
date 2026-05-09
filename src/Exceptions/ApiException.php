<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class ApiException extends Exception implements JenkinsException
{
    public function __construct(
        string $message,
        int $code,
        private readonly ?ResponseInterface $response = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP response instance.
     *
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
