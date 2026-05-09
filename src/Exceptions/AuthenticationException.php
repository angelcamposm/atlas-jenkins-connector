<?php

declare(strict_types=1);

namespace Atlas\Connectors\Jenkins\Exceptions;

use Exception;

/**
 * Exception thrown when authentication fails.
 */
final class AuthenticationException extends Exception implements JenkinsException
{
}
