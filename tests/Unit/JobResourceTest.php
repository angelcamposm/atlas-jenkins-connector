<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\JenkinsClient;
use Atlas\Connectors\Jenkins\Resources\JobResource;
use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

/**
 * @covers \Atlas\Connectors\Jenkins\Resources\JobResource
 */
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
}
