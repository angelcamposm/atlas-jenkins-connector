<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Tests\Unit;

use Atlas\Connectors\Jenkins\Exceptions\ApiException;
use Atlas\Connectors\Jenkins\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ApiException::class)]
final class ApiExceptionTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $response = new Response(500);
        $exception = new ApiException('Error', 500, $response);

        $this->assertEquals('Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
        $this->assertSame($response, $exception->getResponse());
    }
}
