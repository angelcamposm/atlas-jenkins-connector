# Getting Started

## Installation

You can install the package via Composer:

```bash
composer require acamposm/atlas-jenkins-connector
```

## Client Initialization

To start interacting with Jenkins, create an instance of the `JenkinsClient`.

```php
use Atlas\Connectors\Jenkins\JenkinsClient;

$client = new JenkinsClient(
    baseUrl: 'https://jenkins.example.com',
    username: 'admin',      // Optional
    apiToken: 'your-token', // Optional
    timeout: 30.0,          // Optional (float)
    maxRetries: 3           // Optional (int)
);
```

### Reliability Features

By default, the client is configured with:
- **Timeout:** 30 seconds for all requests.
- **Retries:** 3 automatic retries for transient failures (connection issues and 5xx server errors).
- **Backoff:** Exponential backoff strategy to avoid overwhelming the server.

### Custom HTTP Client

If you need specific Guzzle configurations, you can pass your own `ClientInterface` implementation:

```php
$customClient = new \GuzzleHttp\Client(['timeout' => 10]);
$client = new JenkinsClient('https://jenkins.example.com', null, null, $customClient);
```
