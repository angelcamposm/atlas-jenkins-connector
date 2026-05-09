# Build Management

The `BuildResource` handles operations related to specific Jenkins builds (runs). Access it via `$client->builds()`.

## Build Logs

Retrieve the plain-text console output of a build.

```php
$logs = $client->builds()->logs('folder/my-job', 123);
```

## Build Description

Update the description of a specific build run.

```php
$client->builds()->updateDescription('my-job', 123, 'Build completed successfully');
```

## Path Resolution

Note that for all build operations, the job path is automatically resolved to the Jenkins format (`job/FOLDER/job/JOB/BUILD_NUMBER`). You can provide paths as `folder/subfolder/jobname`.
