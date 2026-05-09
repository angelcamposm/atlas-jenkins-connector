<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Resources;

use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Exceptions\JenkinsException;
use JsonException;

/**
 * Resource for managing Jenkins users.
 */
final class UserResource extends AbstractResource
{
    /**
     * List all users.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function list(): array
    {
        $data = $this->decodeResponse(
            $this->request('GET', 'asynchPeople/api/json')
        );

        return $data['users'] ?? [];
    }

    /**
     * Get user details.
     *
     * @param string $username The username.
     *
     * @return array<string, mixed>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function get(string $username): array
    {
        return $this->decodeResponse(
            $this->request('GET', "user/{$username}/api/json")
        );
    }

    /**
     * Delete a user.
     *
     * @param string $username The username to delete.
     *
     * @throws JenkinsException
     */
    public function delete(string $username): void
    {
        $this->request('POST', "user/{$username}/doDelete");
    }
}
