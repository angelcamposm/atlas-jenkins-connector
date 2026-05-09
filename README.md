<p align="center">
  <img src="https://github.com/angelcamposm/atlas-jenkins-connector/blob/master/docs/images/cover.png" width="600" alt="Atlas Jenkins Connector">
  <p align="center">
    <a href="https://packagist.org/packages/acamposm/atlas-jenkins-connector">
      <img alt="Total Downloads" src="https://img.shields.io/packagist/dt/acamposm/atlas-jenkins-connector">
    </a>
    <a href="https://packagist.org/packages/acamposm/atlas-jenkins-connector">
      <img alt="Latest Version" src="https://img.shields.io/packagist/v/acamposm/atlas-jenkins-connector">
    </a>
    <a href="https://packagist.org/packages/acamposm/atlas-jenkins-connector">
      <img alt="Packagist License" src="https://img.shields.io/packagist/l/acamposm/atlas-jenkins-connector">
    </a>
  </p>
</p>

---

# Atlas Jenkins Connector

A robust, modern PHP API client for Jenkins, built for the Atlas ecosystem.

## Features

- **Strictly Typed:** Leverages PHP 8.5 features for maximum reliability.
- **Resource-Based Architecture:** Scalable and easy to navigate.
- **Mock-Ready:** Designed for 100% test coverage using Guzzle MockHandler.
- **Subfolder Support:** Automatic path resolution for jobs in folders.
- **Comprehensive API:** Support for Jobs and Builds.

## Installation

```bash
composer require acamposm/atlas-jenkins-connector
```

## Usage

### Basic Initialization

```php
use Atlas\Connectors\Jenkins\JenkinsClient;

$client = new JenkinsClient(
    baseUrl: 'https://jenkins.example.com',
    username: 'admin',
    apiToken: 'your-api-token'
);
```

### Job Management

```php
// Launch a job
$client->jobs()->build('my-job', ['PARAM' => 'value']);

// Launch a job in a subfolder
$client->jobs()->build('folder/subfolder/my-job');

// Create a job from XML
$xml = file_get_contents('config.xml');
$client->jobs()->create('new-job', $xml, 'my-folder');

// List all jobs in a folder
$jobs = $client->jobs()->list('my-folder');

// Discover ALL jobs recursively (across all subfolders)
$allJobs = $client->jobs()->all();
foreach ($allJobs as $job) {
    echo $job->fullPath . PHP_EOL;
}
```

### Build & Log Management

```php
// Get console logs
$logs = $client->builds()->logs('my-job', 123);

// Update build description
$client->builds()->updateDescription('my-job', 123, 'Build Successful');
```

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## License

MIT
