<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\DataObjects\Job;
use Atlas\Connectors\Jenkins\Enums\JobType;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Job::class)]
final class JobTest extends TestCase
{
    public function test_it_can_be_created_from_array(): void
    {
        $data = [
            '_class' => 'hudson.model.FreeStyleProject',
            'name' => 'my-job',
            'url' => 'https://jenkins.example.com/job/my-job/',
            'color' => 'blue',
            'description' => 'A test job',
        ];

        $job = Job::fromArray($data);

        $expectedArray = [
            'name' => 'my-job',
            'fullPath' => 'my-job',
            'url' => 'https://jenkins.example.com/job/my-job/',
            'type' => JobType::FREE_STYLE_PROJECT->value,
            'color' => 'blue',
            'description' => 'A test job',
        ];

        $this->assertEquals('my-job', $job->name);
        $this->assertEquals('my-job', $job->fullPath);
        $this->assertEquals($data['url'], $job->url);
        $this->assertSame(JobType::FREE_STYLE_PROJECT, $job->type);
        $this->assertEquals('blue', $job->color);
        $this->assertEquals('A test job', $job->description);
        $this->assertEquals($expectedArray, $job->toArray());
    }

    public function test_it_handles_parent_path(): void
    {
        $data = ['name' => 'sub-job'];
        $job = Job::fromArray($data, 'folder1/folder2');

        $this->assertEquals('folder1/folder2/sub-job', $job->fullPath);
    }
}
