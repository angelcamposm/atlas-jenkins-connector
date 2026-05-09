<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Resources;

use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Exceptions\JenkinsException;
use JsonException;

/**
 * Resource for managing Jenkins jobs.
 */
final class JobResource extends AbstractResource
{
    /**
     * Build a job.
     *
     * @param string $path The job path (e.g., 'my-job' or 'folder/my-job').
     * @param array<string, mixed> $parameters Optional build parameters.
     *
     * @throws JenkinsException
     */
    public function build(string $path, array $parameters = []): void
    {
        $resolvedPath = $this->resolvePath($path);
        $endpoint = empty($parameters) ? 'build' : 'buildWithParameters';

        $this->request('POST', "{$resolvedPath}/{$endpoint}", [
            'query' => $parameters,
        ]);
    }

    /**
     * Create a new job from XML definition.
     *
     * @param string $name The name of the new job.
     * @param string $xml The XML configuration of the job.
     * @param string|null $folder Optional folder path where to create the job.
     *
     * @throws JenkinsException
     */
    public function create(string $name, string $xml, ?string $folder = null): void
    {
        $uri = $folder !== null
            ? $this->resolvePath($folder) . '/createItem'
            : 'createItem';

        $this->request('POST', $uri, [
            'query' => ['name' => $name],
            'headers' => ['Content-Type' => 'application/xml'],
            'body' => $xml,
        ]);
    }

    /**
     * Update job description.
     *
     * @param string $path The job path.
     * @param string $description The new description.
     *
     * @throws JenkinsException
     */
    public function updateDescription(string $path, string $description): void
    {
        $resolvedPath = $this->resolvePath($path);

        $this->request('POST', "$resolvedPath/description", [
            'form_params' => ['description' => $description],
        ]);
    }

    /**
     * List job artifacts for a specific build.
     *
     * @param string $path The job path.
     * @param int $buildNumber The build number.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function artifacts(string $path, int $buildNumber): array
    {
        $resolvedPath = $this->resolvePath($path);

        $data = $this->decodeResponse(
            $this->request('GET', "$resolvedPath/$buildNumber/api/json", [
                'query' => ['tree' => 'artifacts[*]'],
            ])
        );

        return $data['artifacts'] ?? [];
    }

    /**
     * Resolve the Jenkins-style path for a job.
     *
     * @param string $path The raw path.
     *
     * @return string
     */
    private function resolvePath(string $path): string
    {
        $path = trim($path, '/');

        if (str_starts_with($path, 'job/')) {
            return $path;
        }

        $parts = explode('/', $path);

        return 'job/' . implode('/job/', $parts);
    }
}
