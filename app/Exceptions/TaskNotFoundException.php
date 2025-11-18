<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class TaskNotFoundException extends Exception
{
    public function __construct(int $taskId, string $message = 'Task not found', int $code = 404)
    {
        parent::__construct("$message (ID: $taskId)", $code);
    }
}
