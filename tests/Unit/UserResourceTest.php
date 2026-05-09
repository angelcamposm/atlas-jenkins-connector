<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\JenkinsClient;
use Atlas\Connectors\Jenkins\Resources\UserResource;
use Atlas\Connectors\Jenkins\AbstractResource;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

/**
 * @covers \Atlas\Connectors\Jenkins\Resources\UserResource
 */
#[CoversClass(UserResource::class)]
#[UsesClass(AbstractResource::class)]
#[UsesClass(JenkinsClient::class)]
final class UserResourceTest extends TestCase
{
    public function test_it_can_list_users(): void
    {
        $mockData = [
            'users' => [
                ['user' => ['fullName' => 'Admin', 'id' => 'admin']],
            ],
        ];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $users = $client->users()->list();

        $request = $this->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('asynchPeople/api/json', $request->getUri()->getPath());
        $this->assertEquals($mockData['users'], $users);
    }

    public function test_it_can_get_user(): void
    {
        $mockData = ['fullName' => 'Admin', 'id' => 'admin'];

        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, $mockData),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $user = $client->users()->get('admin');

        $request = $this->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('user/admin/api/json', $request->getUri()->getPath());
        $this->assertEquals($mockData, $user);
    }

    public function test_it_can_delete_user(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $client->users()->delete('baduser');

        $request = $this->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('user/baduser/doDelete', $request->getUri()->getPath());
    }

    public function test_it_returns_empty_array_if_no_users_key(): void
    {
        $httpClient = $this->createMockHttpClient([
            $this->jsonResponse(200, []),
        ]);

        $client = new JenkinsClient('https://jenkins.example.com', null, null, $httpClient);
        $users = $client->users()->list();

        $this->assertEquals([], $users);
    }
}
