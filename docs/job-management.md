# Job Management

The `JobResource` provides methods to manage Jenkins jobs. Access it via `$client->jobs()`.

## Listing Jobs

### List at a specific path
Returns an array of `Job` objects at the specified path (root or folder).

```php
$jobs = $client->jobs()->list('my-folder');
```

### Recursive Discovery (`all()`)
Recursively traverses all folders and subfolders to find every individual job.

```php
$allJobs = $client->jobs()->all();
foreach ($allJobs as $job) {
    echo $job->fullPath; // e.g., "production/web-apps/deploy"
}
```

## Job Operations

### Building a Job
Launch a job with or without parameters.

```php
// Without parameters
$client->jobs()->build('my-job');

// With parameters
$client->jobs()->build('my-job', ['BRANCH' => 'main']);
```

### Creating a Job
Create a new job from an XML definition.

```php
$xml = '<project>...</project>';
$client->jobs()->create('new-job', $xml, 'optional-folder');
```

### Updating Description
Update the description of a job or pipeline.

```php
$client->jobs()->updateDescription('my-job', 'Updated via API');
```

### Listing Artifacts
Retrieve artifacts for a specific build of a job.

```php
$artifacts = $client->jobs()->artifacts('my-job', 42);
```
