<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\JenkinsClient;
use Atlas\Connectors\Jenkins\Resources\JobResource;
use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\DataObjects\Job;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(JobResource::class)]
#[UsesClass(AbstractResource::class)]
#[UsesClass(JenkinsClient::class)]
final class JobResourceTest extends TestCase
{
    public function test_it_can_build_a_job_without_parameters(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(201, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->build('my-job');

        $request = $this->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('job/my-job/build', $request->getUri()->getPath());
    }

    public function test_it_can_build_a_job_with_parameters(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(201, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->build('my-job', ['foo' => 'bar']);

        $request = $this->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('job/my-job/buildWithParameters', $request->getUri()->getPath());
        $this->assertEquals('foo=bar', $request->getUri()->getQuery());
    }

    public function test_it_can_build_a_job_in_a_folder(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(201, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->build('folder1/folder2/my-job');

        $request = $this->getLastRequest();
        $this->assertEquals('job/folder1/job/folder2/job/my-job/build', $request->getUri()->getPath());
    }

    public function test_it_can_create_a_job(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $xml = '<project></project>';
        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->create('new-job', $xml);

        $request = $this->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('createItem', $request->getUri()->getPath());
        $this->assertEquals('name=new-job', $request->getUri()->getQuery());
        $this->assertEquals('application/xml', $request->getHeaderLine('Content-Type'));
        $this->assertEquals($xml, $request->getBody()->getContents());
    }

    public function test_it_can_create_a_job_in_a_folder(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $xml = '<project></project>';
        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->create('new-job', $xml, 'my-folder');

        $request = $this->getLastRequest();
        $this->assertEquals('job/my-folder/createItem', $request->getUri()->getPath());
        $this->assertEquals('name=new-job', $request->getUri()->getQuery());
    }

    public function test_it_can_update_description(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->updateDescription('my-job', 'New Description');

        $request = $this->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('job/my-job/description', $request->getUri()->getPath());
        $this->assertEquals('description=New+Description', $request->getBody()->getContents());
    }

    public function test_it_can_get_artifacts(): void
    {
        $mockData = [
            'artifacts' => [
                ['displayPath' => 'artifact1.txt', 'fileName' => 'artifact1.txt', 'relativePath' => 'artifact1.txt'],
            ],
        ];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $artifacts = $client->jobs()->artifacts('my-job', 123);

        $request = $this->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('job/my-job/123/api/json', $request->getUri()->getPath());
        $this->assertEquals('tree=artifacts%5B%2A%5D', $request->getUri()->getQuery());
        $this->assertEquals($mockData['artifacts'], $artifacts);
    }

    public function test_it_handles_path_already_starting_with_job(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(201, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->build('job/my-job');

        $request = $this->getLastRequest();
        $this->assertEquals('job/my-job/build', $request->getUri()->getPath());
    }

    public function test_it_returns_empty_array_if_no_artifacts_key(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $artifacts = $client->jobs()->artifacts('my-job', 123);

        $this->assertEquals([], $artifacts);
    }

    public function test_it_can_list_jobs(): void
    {
        $mockData = [
            'jobs' => [
                ['_class' => 'hudson.model.FreeStyleProject', 'name' => 'job1', 'url' => '...', 'color' => 'blue'],
            ],
        ];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $jobs = $client->jobs()->list();

        $request = $this->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('api/json', $request->getUri()->getPath());
        $this->assertEquals('tree=jobs%5B_class%2Cname%2Curl%2Ccolor%2Cdescription%2Cjobs%5Bname%5D%5D', $request->getUri()->getQuery());
        
        $this->assertCount(1, $jobs);
        $this->assertInstanceOf(Job::class, $jobs[0]);
        $this->assertEquals('job1', $jobs[0]->name);
    }

    public function test_it_can_list_jobs_in_a_folder(): void
    {
        $mockData = ['jobs' => []];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->jobs()->list('my-folder');

        $request = $this->getLastRequest();
        $this->assertEquals('job/my-folder/api/json', $request->getUri()->getPath());
    }

    public function test_it_can_get_job_details(): void
    {
        $mockData = ['name' => 'my-job', 'description' => '...'];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $job = $client->jobs()->get('my-job');

        $request = $this->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('job/my-job/api/json', $request->getUri()->getPath());
        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals('my-job', $job->name);
    }

    public function test_it_can_list_jobs_recursively(): void
    {
        $httpClient = $this->createMockHttpClient([
            // First call: list root
            $this->jsonResponse(200, [
                'jobs' => [
                    [
                        '_class' => 'com.cloudbees.hudson.plugins.folder.Folder',
                        'name' => 'folder1',
                        'jobs' => [['name' => 'nested-job']],
                    ],
                    [
                        '_class' => 'hudson.model.FreeStyleProject',
                        'name' => 'job-at-root',
                    ],
                ],
            ]),
            // Second call: list folder1
            $this->jsonResponse(200, [
                'jobs' => [
                    [
                        '_class' => 'hudson.model.FreeStyleProject',
                        'name' => 'nested-job',
                    ],
                ],
            ]),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $allJobs = $client->jobs()->all();

        $this->assertCount(2, $allJobs);
        $this->assertInstanceOf(Job::class, $allJobs[0]);
        $this->assertEquals('job-at-root', $allJobs[0]->name);
        $this->assertEquals('job-at-root', $allJobs[0]->fullPath);
        $this->assertEquals('nested-job', $allJobs[1]->name);
        $this->assertEquals('folder1/nested-job', $allJobs[1]->fullPath);
    }
}
