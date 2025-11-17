<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\TaskDto;
use App\Models\Task;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function all(): Collection;

    public function findOrFail(int $id): Task;

    public function create(TaskDto $dto): Task;

    public function update(Task $task, TaskDto $dto): Task;

    public function delete(Task $task): void;
}
