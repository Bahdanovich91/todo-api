<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

final class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    public function index(): JsonResponse
    {
        $tasks = TaskResource::collection($this->taskService->list());

        return response()->json([
            'success' => true,
            'data' => $tasks->resolve(),
        ], ResponseAlias::HTTP_OK);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = new TaskResource($this->taskService->create($request->toDto()));

        return response()->json([
            'success' => true,
            'data' => $task->resolve(),
        ], ResponseAlias::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $task = new TaskResource($this->taskService->show($id));

        return response()->json([
            'success' => true,
            'data' => $task->resolve(),
        ], ResponseAlias::HTTP_OK);
    }

    public function update(TaskRequest $request, int $id): JsonResponse
    {
        $task = new TaskResource($this->taskService->update($id, $request->toDto()));

        return response()->json([
            'success' => true,
            'data' => $task->resolve(),
        ], ResponseAlias::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->taskService->delete($id);

        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
