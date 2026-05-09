<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Resources;

use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\DataObjects\Job;
use Atlas\Connectors\Jenkins\Enums\JobType;
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

        $this->request('POST', "{$resolvedPath}/description", [
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
            $this->request('GET', "{$resolvedPath}/{$buildNumber}/api/json", [
                'query' => ['tree' => 'artifacts[*]'],
            ])
        );

        return $data['artifacts'] ?? [];
    }

    /**
     * List all jobs at a specific path.
     *
     * @param string|null $path Optional folder path to list jobs from.
     *
     * @return array<int, Job>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function list(?string $path = null): array
    {
        return array_map(
            fn (array $item) => Job::fromArray($item, $path),
            $this->fetchRawItems($path)
        );
    }

    /**
     * List all jobs recursively from a specific path.
     *
     * @param string|null $path Optional folder path to start discovery from.
     *
     * @return array<int, Job>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function all(?string $path = null): array
    {
        $items = $this->fetchRawItems($path);
        $allJobs = [];
        $folders = [];

        foreach ($items as $item) {
            $type = JobType::fromClass($item['_class'] ?? null);

            // We exclude "pure" folders from the final job list.
            if (! ($type?->isFolder() ?? false)) {
                $allJobs[] = Job::fromArray($item, $path);
            }

            // Detect if it acts as a container for recursion.
            if (array_key_exists('jobs', $item)) {
                $folders[] = $item;
            }
        }

        foreach ($folders as $folder) {
            $folderPath = ($path ? trim($path, '/') . '/' : '') . $folder['name'];
            $allJobs = array_merge($allJobs, $this->all($folderPath));
        }

        return $allJobs;
    }

    /**
     * Get detailed information about a job.
     *
     * @param string $path The job path.
     *
     * @return Job
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function get(string $path): Job
    {
        $resolvedPath = $this->resolvePath($path);

        $data = $this->decodeResponse(
            $this->request('GET', "{$resolvedPath}/api/json")
        );

        $parts = explode('/', trim($path, '/'));
        $parentPath = count($parts) > 1 ? implode('/', array_slice($parts, 0, -1)) : null;

        return Job::fromArray($data, $parentPath);
    }

    /**
     * Fetch raw items from the Jenkins API.
     *
     * @param string|null $path
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    private function fetchRawItems(?string $path = null): array
    {
        $uri = $path !== null ? $this->resolvePath($path) . '/api/json' : 'api/json';

        $data = $this->decodeResponse(
            $this->request('GET', $uri, [
                'query' => [
                    'tree' => 'jobs[_class,name,url,color,description,jobs[name]]',
                ],
            ])
        );

        return $data['jobs'] ?? [];
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
