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
    apiToken: 'your-token'  // Optional
);
```

### Custom HTTP Client

If you need specific Guzzle configurations, you can pass your own `ClientInterface` implementation:

```php
$customClient = new \GuzzleHttp\Client(['timeout' => 10]);
$client = new JenkinsClient('https://jenkins.example.com', null, null, $customClient);
```
