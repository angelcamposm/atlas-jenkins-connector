# Atlas Jenkins Connector - Project Instructions

This document provides foundational mandates and architectural guidance for the `atlas-jenkins-connector` project.

## Architecture & Design Patterns

### Resource-Based Architecture
The project follows a resource-based architecture. All Jenkins API resources must extend `Atlas\Connectors\Jenkins\AbstractResource`.
- Resources should be located in `src/Resources/`.
- Resources are exposed via the `Atlas\Connectors\Jenkins\JenkinsClient`.

### Jenkins Path Resolution
Jenkins uses a specific path format for jobs in folders: `job/FOLDER_NAME/job/JOB_NAME`.
- All resource methods accepting a job path must use the `resolvePath()` helper to ensure consistent formatting.
- The helper should handle both raw paths (`folder/job`) and already formatted paths (`job/folder/job/job`).

## Coding Standards & Conventions

- **PHP Version:** Strictly target PHP 8.5+.
- **Type Safety:** `declare(strict_types=1);` is mandatory in all PHP files. Use explicit types for parameters, return values, and properties.
- **Modern Features:** Favor modern PHP features such as Property Hooks for public read-only access to internal state.
- **Style:** Adhere to PER Coding Style (PER-CS). Use `composer format` to maintain consistency.

## Testing & Validation

### Coverage Mandate
- **100% Test Coverage:** All new features and bug fixes must maintain 100% line and method coverage.
- **Request Inspection:** Utilize the `getLastRequest()` method in `TestCase` to verify the exact HTTP method, URI, headers, and body sent to the Jenkins API.

### Mocking
- Always use the `createMockHttpClient()` helper in `TestCase` to mock Jenkins API responses. Do not make real network calls in unit tests.

## Development Workflow

1. **Verify:** Run `composer test` and `composer analyse` before submitting changes.
2. **Format:** Run `composer format` to ensure compliance with coding standards.
3. **Documentation:** Every class and public method must have comprehensive PHPDoc.
