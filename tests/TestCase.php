<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;

abstract class TestCase extends BaseTestCase
{
    /** @var array<int, array<string, mixed>> */
    protected array $history = [];

    protected function createMockHttpClient(array $responses): Client
    {
        $this->history = [];
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($this->history));

        return new Client(['handler' => $handlerStack]);
    }

    protected function getLastRequest(): RequestInterface
    {
        if (empty($this->history)) {
            $this->fail('No requests were made.');
        }

        return $this->history[count($this->history) - 1]['request'];
    }

    /**
     * Create a JSON response.
     *
     * @param int $status The HTTP status code.
     * @param array<string, mixed> $body The response body as an array.
     *
     * @return Response
     */
    protected function jsonResponse(int $status, array $body): Response
    {
        return new Response(
            $status,
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );
    }
}
