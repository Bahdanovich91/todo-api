<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\TaskDto;
use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

final readonly class TaskRepository implements RepositoryInterface
{
    public function __construct(private Task $model)
    {
    }

    public function all(): Collection
    {
        /** @var EloquentCollection<int, Task> $tasks */
        $tasks = $this->query()
            ->latest('id')
            ->get();

        return $tasks->collect();
    }

    public function findOrFail(int $id): Task
    {
        /** @var Task $task */
        $task = Task::where('id', $id)->first();
        if (!$task) {
            throw new TaskNotFoundException($id);
        }

        return $task;
    }

    public function create(TaskDto $dto): Task
    {
        /** @var Task $task */
        $task = $this->query()->create($dto->toArray());

        return $task;
    }

    public function update(Task $task, TaskDto $dto): Task
    {
        $task->fill($dto->toArray());
        $task->save();

        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    private function query(): Builder
    {
        return $this->model->newQuery();
    }
}
