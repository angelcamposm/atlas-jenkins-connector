<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\JenkinsClient;
use Atlas\Connectors\Jenkins\Resources\BuildResource;
use Atlas\Connectors\Jenkins\Resources\JobResource;
use Atlas\Connectors\Jenkins\Resources\SystemResource;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @covers \Atlas\Connectors\Jenkins\JenkinsClient
 */
#[CoversClass(JenkinsClient::class)]
final class JenkinsClientTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $client = new JenkinsClient('https://jenkins.example.com');

        $this->assertInstanceOf(JenkinsClient::class, $client);
        $this->assertInstanceOf(ClientInterface::class, $client->httpClient);
    }

    public function test_it_normalizes_base_url(): void
    {
        $client = new JenkinsClient('https://jenkins.example.com');
        $this->assertEquals('https://jenkins.example.com/', $client->httpClient->getConfig('base_uri')->__toString());

        $client = new JenkinsClient('https://jenkins.example.com/');
        $this->assertEquals('https://jenkins.example.com/', $client->httpClient->getConfig('base_uri')->__toString());
    }

    public function test_it_can_be_instantiated_with_auth(): void
    {
        $client = new JenkinsClient('https://jenkins.example.com', 'user', 'token');

        $this->assertEquals(['user', 'token'], $client->httpClient->getConfig('auth'));
    }

    public function test_it_can_be_instantiated_with_custom_client(): void
    {
        $customClient = $this->createMockHttpClient([]);
        $client = new JenkinsClient('https://jenkins.example.com', null, null, $customClient);

        $this->assertSame($customClient, $client->httpClient);
    }

    public function test_it_can_access_system_resource(): void
    {
        $client = new JenkinsClient('https://jenkins.example.com');

        $this->assertInstanceOf(SystemResource::class, $client->system());
    }

    public function test_it_can_access_jobs_resource(): void
    {
        $client = new JenkinsClient('https://jenkins.example.com');

        $this->assertInstanceOf(JobResource::class, $client->jobs());
    }

    public function test_it_can_access_builds_resource(): void
    {
        $client = new JenkinsClient('https://jenkins.example.com');

        $this->assertInstanceOf(BuildResource::class, $client->builds());
    }
}
