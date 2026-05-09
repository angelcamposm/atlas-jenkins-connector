<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins;

use Atlas\Connectors\Jenkins\Exceptions\ApiException;
use Atlas\Connectors\Jenkins\Exceptions\AuthenticationException;
use Atlas\Connectors\Jenkins\Exceptions\JenkinsException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for all Jenkins resources.
 */
abstract class AbstractResource
{
    /**
     * Create a new resource instance.
     *
     * @param ClientInterface $httpClient The HTTP client instance.
     */
    public function __construct(
        protected readonly ClientInterface $httpClient,
    ) {
    }

    /**
     * Perform an HTTP request.
     *
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param string $uri The URI to request.
     * @param array<string, mixed> $options Request options (query, headers, body, etc.).
     *
     * @return ResponseInterface
     *
     * @throws JenkinsException
     */
    protected function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->httpClient->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
            $code = $response?->getStatusCode() ?? 500;

            if ($code === 401) {
                throw new AuthenticationException('Unauthorized', 401, $e);
            }

            throw new ApiException(
                $e->getMessage(),
                $code,
                $response,
                $e
            );
        }
    }

    /**
     * Decode a JSON response.
     *
     * @param ResponseInterface $response The response instance.
     *
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    protected function decodeResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}
