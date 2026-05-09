<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Enums;

/**
 * Enumeration of Jenkins Job and Container types.
 */
enum JobType: string
{
    case FOLDER = 'com.cloudbees.hudson.plugins.folder.Folder';
    case WORKFLOW_JOB = 'org.jenkinsci.plugins.workflow.job.WorkflowJob';
    case WORKFLOW_MULTI_BRANCH_PROJECT = 'org.jenkinsci.plugins.workflow.multibranch.WorkflowMultiBranchProject';
    case FREE_STYLE_PROJECT = 'hudson.model.FreeStyleProject';
    case MATRIX_PROJECT = 'hudson.matrix.MatrixProject';
    case MAVEN_MODULE_SET = 'hudson.maven.MavenModuleSet';
    case ORGANIZATION_FOLDER = 'jenkins.branch.OrganizationFolder';
    case EXTERNAL_JOB = 'hudson.model.ExternalJob';

    /**
     * Determine if the type acts as a container for other jobs.
     *
     * @return bool
     */
    public function isContainer(): bool
    {
        return match ($this) {
            self::FOLDER,
            self::WORKFLOW_MULTI_BRANCH_PROJECT,
            self::ORGANIZATION_FOLDER => true,
            default => false,
        };
    }

    /**
     * Determine if the type is a "pure" folder/organizational unit.
     *
     * @return bool
     */
    public function isFolder(): bool
    {
        return match ($this) {
            self::FOLDER,
            self::ORGANIZATION_FOLDER => true,
            default => false,
        };
    }

    /**
     * Get a JobType from a string class name.
     *
     * @param string|null $class
     *
     * @return self|null
     */
    public static function fromClass(?string $class): ?self
    {
        if ($class === null) {
            return null;
        }

        return self::tryFrom($class);
    }
}
