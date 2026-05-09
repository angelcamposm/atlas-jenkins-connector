<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\DataObjects;

use Atlas\Connectors\Jenkins\Enums\JobType;

/**
 * Data Transfer Object representing a Jenkins Job.
 */
readonly class Job
{
    /**
     * Create a new Job data object.
     *
     * @param string $name The name of the job.
     * @param string $fullPath The full hierarchical path of the job.
     * @param string $url The Jenkins URL for the job.
     * @param JobType|null $type The type of the job.
     * @param string|null $color The current status color.
     * @param string|null $description The job description.
     */
    public function __construct(
        public string $name,
        public string $fullPath,
        public string $url,
        public ?JobType $type = null,
        public ?string $color = null,
        public ?string $description = null,
    ) {
    }

    /**
     * Get the job data as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'fullPath' => $this->fullPath,
            'url' => $this->url,
            'type' => $this->type?->value,
            'color' => $this->color,
            'description' => $this->description,
        ];
    }

    /**
     * Create a Job object from an API response array.
     *
     * @param array<string, mixed> $data
     * @param string|null $parentPath
     *
     * @return self
     */
    public static function fromArray(array $data, ?string $parentPath = null): self
    {
        $name = $data['name'] ?? '';
        $fullPath = ($parentPath ? trim($parentPath, '/') . '/' : '') . $name;

        return new self(
            name: $name,
            fullPath: $fullPath,
            url: $data['url'] ?? '',
            type: JobType::fromClass($data['_class'] ?? null),
            color: $data['color'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
