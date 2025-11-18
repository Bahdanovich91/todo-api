<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\TaskDto;
use App\Exceptions\TaskNotFoundException;
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

    /**
     * @throws TaskNotFoundException
     */
    public function show(int $taskId): Task
    {
        return $this->findOrFail($taskId);
    }

    /**
     * @throws TaskNotFoundException
     */
    public function update(int $taskId, TaskDto $dto): Task
    {
        $task = $this->findOrFail($taskId);

        return $this->repository->update($task, $dto);
    }

    /**
     * @throws TaskNotFoundException
     */
    public function delete(int $taskId): void
    {
        $task = $this->findOrFail($taskId);
        $this->repository->delete($task);
    }

    /**
     * @throws TaskNotFoundException
     */
    private function findOrFail(int $id): Task
    {
        $task = $this->repository->find($id);
        if (!$task) {
            throw new TaskNotFoundException($id);
        }

        return $task;
    }
}
