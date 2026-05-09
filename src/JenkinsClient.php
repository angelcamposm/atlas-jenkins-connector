<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins;

use Atlas\Connectors\Jenkins\Resources\BuildResource;
use Atlas\Connectors\Jenkins\Resources\JobResource;
use Atlas\Connectors\Jenkins\Resources\SystemResource;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * The main client for interacting with the Jenkins API.
 */
final class JenkinsClient
{
    /**
     * The HTTP client used to make requests.
     */
    public ClientInterface $httpClient {
        get => $this->httpClient;
    }

    /**
     * Create a new Jenkins client instance.
     *
     * @param string $baseUrl The base URL of the Jenkins server.
     * @param string|null $username The username for authentication.
     * @param string|null $apiToken The API token for authentication.
     * @param ClientInterface|null $httpClient A custom HTTP client instance.
     * @param float $timeout The request timeout in seconds.
     * @param int $maxRetries The maximum number of retries for failed requests.
     */
    public function __construct(
        string $baseUrl,
        ?string $username = null,
        ?string $apiToken = null,
        ?ClientInterface $httpClient = null,
        float $timeout = 30.0,
        int $maxRetries = 3,
        ?HandlerStack $handlerStack = null,
    ) {
        $baseUrl = rtrim($baseUrl, '/') . '/';

        if ($httpClient !== null) {
            $this->httpClient = $httpClient;
            return;
        }

        $handlerStack ??= HandlerStack::create();
        $handlerStack->push($this->createRetryMiddleware($maxRetries));

        $config = [
            'base_uri' => $baseUrl,
            'handler' => $handlerStack,
            'timeout' => $timeout,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($username !== null && $apiToken !== null) {
            $config['auth'] = [$username, $apiToken];
        }

        $this->httpClient = new Client($config);
    }

    /**
     * Create retry middleware with exponential backoff.
     *
     * @param int $maxRetries
     *
     * @return callable
     */
    private function createRetryMiddleware(int $maxRetries): callable
    {
        return Middleware::retry(
            function (
                int $retries,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?Throwable $exception = null
            ) use ($maxRetries): bool {
                if ($retries >= $maxRetries) {
                    return false;
                }

                // Retry on connection errors or server errors (5xx)
                if ($exception instanceof Throwable || ($response && $response->getStatusCode() >= 500)) {
                    return true;
                }

                return false;
            },
            function (int $retries): int {
                return (int) (2 ** ($retries - 1) * 1000);
            }
        );
    }

    /**
     * Get the system resource.
     *
     * @return SystemResource
     */
    public function system(): SystemResource
    {
        return new SystemResource($this->httpClient);
    }

    /**
     * Get the job's resource.
     *
     * @return JobResource
     */
    public function jobs(): JobResource
    {
        return new JobResource($this->httpClient);
    }

    /**
     * Get the build's resource.
     *
     * @return BuildResource
     */
    public function builds(): BuildResource
    {
        return new BuildResource($this->httpClient);
    }
}
