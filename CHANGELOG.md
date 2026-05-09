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
- `BuildResource` for managing Jenkins builds.
  - Retrieve console logs (text).
  - Update build descriptions.
- `UserResource` for managing Jenkins users.
  - List all users.
  - Get user details.
  - Delete users.
- `JenkinsClient` updates to expose the new resources.
- Comprehensive unit tests with 100% coverage.
- Full PHPDoc documentation for all classes and methods.
- Support for jobs in subfolders with automatic path resolution.
