# Architecture

## Design Patterns

### Resource-Based
Each Jenkins API segment is encapsulated in its own Resource class (e.g., `JobResource`, `UserResource`). All resources extend `AbstractResource`, which handles the HTTP communication and error processing.

### Immutability
Data is transferred via `readonly` DataObjects like `Job`, ensuring that state is predictable and thread-safe.

### Exception Strategy
- `JenkinsException`: Base interface for all package exceptions.
- `ApiException`: Thrown when the Jenkins API returns an error response (includes the PSR-7 `ResponseInterface`).
- `AuthenticationException`: Specialized exception for 401 Unauthorized errors.

## Path Resolution

The library simplifies Jenkins' complex nested job URL structure. 
- Input: `folder/subfolder/my-job`
- Resolved: `job/folder/job/subfolder/job/my-job`

This resolution is handled internally by the `resolvePath()` method in resource classes, allowing developers to use intuitive paths.

## Testing

Unit tests use the Guzzle `MockHandler` to simulate Jenkins API responses. The `TestCase` class includes a `history` container to allow verification of:
1. HTTP Method
2. Requested URI
3. Request Headers
4. Request Body/Query Parameters
