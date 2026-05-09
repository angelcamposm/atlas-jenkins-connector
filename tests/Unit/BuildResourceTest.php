<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\JenkinsClient;
use Atlas\Connectors\Jenkins\Resources\BuildResource;
use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

/**
 * @covers \Atlas\Connectors\Jenkins\Resources\BuildResource
 */
#[CoversClass(BuildResource::class)]
#[UsesClass(AbstractResource::class)]
#[UsesClass(JenkinsClient::class)]
final class BuildResourceTest extends TestCase
{
    public function test_it_can_get_logs(): void
    {
        $logContent = 'Build started...';
        $httpClient = $this->createMockHttpClient([
            new Response(200, [], $logContent),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $logs = $client->builds()->logs('my-job', 1);

        $request = $this->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('job/my-job/1/consoleText', $request->getUri()->getPath());
        $this->assertEquals($logContent, $logs);
    }

    public function test_it_can_update_description(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->builds()->updateDescription('my-job', 1, 'Build Successful');

        $request = $this->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('job/my-job/1/submitDescription', $request->getUri()->getPath());
        $this->assertEquals('description=Build+Successful', $request->getBody()->getContents());
    }

    public function test_it_handles_path_already_starting_with_job(): void
    {
        $httpClient = $this->createMockHttpClient([
            new Response(200, [], 'logs'),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->builds()->logs('job/my-job', 1);

        $request = $this->getLastRequest();
        $this->assertEquals('job/my-job/1/consoleText', $request->getUri()->getPath());
    }
}
