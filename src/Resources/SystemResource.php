<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Resources;

use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Exceptions\JenkinsException;
use JsonException;

/**
 * Resource for system-level Jenkins information.
 */
final class SystemResource extends AbstractResource
{
    /**
     * Get Jenkins version and basic info.
     *
     * @return array<string, mixed>
     *
     * @throws JsonException
     * @throws JenkinsException
     */
    public function info(): array
    {
        return $this->decodeResponse(
            $this->request('GET', 'api/json')
        );
    }
}
