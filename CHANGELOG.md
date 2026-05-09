# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-05-09

### Added
- `JobResource` for managing Jenkins jobs.
  - Launch jobs with or without parameters.
  - Create jobs from XML definitions.
  - Update job/pipeline descriptions.
  - List job artifacts.
  - List all jobs at a specific path.
  - Recursive job discovery (`all()`) across all folders and subfolders.
- `JobType` enum for type-safe job and container classification.
- `BuildResource` for managing Jenkins builds.
  - Retrieve console logs (text).
  - Update build descriptions.
- `JenkinsClient` updates to expose the new resources.
- Comprehensive unit tests with 100% coverage.
- Full PHPDoc documentation for all classes and methods.
- Support for jobs in subfolders with automatic path resolution.
- Configurable timeouts and automatic retries with exponential backoff.
