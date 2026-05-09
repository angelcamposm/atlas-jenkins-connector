<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins;

use Atlas\Connectors\Jenkins\Resources\BuildResource;
use Atlas\Connectors\Jenkins\Resources\JobResource;
use Atlas\Connectors\Jenkins\Resources\SystemResource;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

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
     */
    public function __construct(
        string $baseUrl,
        ?string $username = null,
        ?string $apiToken = null,
        ?ClientInterface $httpClient = null,
    ) {
        $baseUrl = rtrim($baseUrl, '/') . '/';

        $config = [
            'base_uri' => $baseUrl,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($username !== null && $apiToken !== null) {
            $config['auth'] = [$username, $apiToken];
        }

        $this->httpClient = $httpClient ?? new Client($config);
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
