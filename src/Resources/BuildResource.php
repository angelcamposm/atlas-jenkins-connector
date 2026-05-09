<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Resources;

use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Exceptions\JenkinsException;

/**
 * Resource for managing Jenkins builds.
 */
final class BuildResource extends AbstractResource
{
    /**
     * Get console output (logs) of a build.
     *
     * @param string $path The job path.
     * @param int $buildNumber The build number.
     *
     * @return string
     *
     * @throws JenkinsException
     */
    public function logs(string $path, int $buildNumber): string
    {
        $resolvedPath = $this->resolvePath($path);

        return $this->request('GET', "$resolvedPath/$buildNumber/consoleText")
            ->getBody()
            ->getContents();
    }

    /**
     * Update build (run) description.
     *
     * @param string $path The job path.
     * @param int $buildNumber The build number.
     * @param string $description The new description.
     *
     * @throws JenkinsException
     */
    public function updateDescription(string $path, int $buildNumber, string $description): void
    {
        $resolvedPath = $this->resolvePath($path);

        $this->request('POST', "$resolvedPath/$buildNumber/submitDescription", [
            'form_params' => ['description' => $description],
        ]);
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
