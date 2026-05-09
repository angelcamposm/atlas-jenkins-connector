<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\Enums\JobType;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(JobType::class)]
final class JobTypeTest extends TestCase
{
    public function test_it_identifies_containers(): void
    {
        $this->assertTrue(JobType::FOLDER->isContainer());
        $this->assertTrue(JobType::WORKFLOW_MULTI_BRANCH_PROJECT->isContainer());
        $this->assertTrue(JobType::ORGANIZATION_FOLDER->isContainer());
        $this->assertFalse(JobType::WORKFLOW_JOB->isContainer());
        $this->assertFalse(JobType::FREE_STYLE_PROJECT->isContainer());
    }

    public function test_it_identifies_pure_folders(): void
    {
        $this->assertTrue(JobType::FOLDER->isFolder());
        $this->assertTrue(JobType::ORGANIZATION_FOLDER->isFolder());
        $this->assertFalse(JobType::WORKFLOW_MULTI_BRANCH_PROJECT->isFolder());
        $this->assertFalse(JobType::WORKFLOW_JOB->isFolder());
    }

    public function test_it_can_be_created_from_class_name(): void
    {
        $this->assertSame(JobType::FOLDER, JobType::fromClass('com.cloudbees.hudson.plugins.folder.Folder'));
        $this->assertSame(JobType::WORKFLOW_JOB, JobType::fromClass('org.jenkinsci.plugins.workflow.job.WorkflowJob'));
        $this->assertNull(JobType::fromClass('UnknownClass'));
        $this->assertNull(JobType::fromClass(null));
    }
}
