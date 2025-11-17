<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\TaskDto;
use App\Models\Task;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Collection;

final readonly class TaskService
{
    public function __construct(
        private RepositoryInterface $repository,
    ) {
    }

    /**
     * @return Collection
     */
    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function create(TaskDto $dto): Task
    {
        return $this->repository->create($dto);
    }

    public function show(int $taskId): Task
    {
        return $this->repository->findOrFail($taskId);
    }

    public function update(int $taskId, TaskDto $dto): Task
    {
        $task = $this->repository->findOrFail($taskId);

        return $this->repository->update($task, $dto);
    }

    public function delete(int $taskId): void
    {
        $task = $this->repository->findOrFail($taskId);
        $this->repository->delete($task);
    }
}
