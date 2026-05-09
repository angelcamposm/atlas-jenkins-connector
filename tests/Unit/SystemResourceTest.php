<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\JenkinsClient;
use Atlas\Connectors\Jenkins\Resources\SystemResource;
use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use Atlas\Connectors\Jenkins\Exceptions\ApiException;
use Atlas\Connectors\Jenkins\Exceptions\AuthenticationException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

/**
 * @covers \Atlas\Connectors\Jenkins\Resources\SystemResource
 */
#[CoversClass(SystemResource::class)]
#[UsesClass(AbstractResource::class)]
#[UsesClass(JenkinsClient::class)]
#[UsesClass(ApiException::class)]
#[UsesClass(AuthenticationException::class)]
final class SystemResourceTest extends TestCase
{
    public function test_it_can_get_info(): void
    {
        $mockData = [
            'nodeDescription' => 'the master Jenkins node',
            'mode' => 'NORMAL',
            'nodeName' => '',
        ];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $info = $client->system()->info();

        $this->assertEquals($mockData, $info);
    }

    public function test_it_throws_authentication_exception_on_401(): void
    {
        $httpClient = $this->createMockHttpClient([
            new Response(401),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Unauthorized');
        $this->expectExceptionCode(401);

        $client->system()->info();
    }

    public function test_it_throws_api_exception_on_other_errors(): void
    {
        $httpClient = $this->createMockHttpClient([
            new Response(500, [], 'Internal Server Error'),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);

        try {
            $client->system()->info();
        } catch (ApiException $e) {
            $this->assertEquals(500, $e->getCode());
            $this->assertStringContainsString('Internal Server Error', $e->getMessage());
            $this->assertInstanceOf(Response::class, $e->getResponse());
            return;
        }

        $this->fail('ApiException was not thrown');
    }
}
