<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

#[OA\Info(
    version: '1.0.0',
    description: 'API для управления задачами.',
    title: 'Todo API'
)]
#[OA\Tag(
    name: 'Tasks',
    description: 'Управление задачами (создание, просмотр, обновление, удаление)'
)]
final class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    #[OA\Get(
        path: '/api/tasks',
        summary: 'Получить список задач',
        tags: ['Tasks'],
        responses: [
            new OA\Response(
                response: ResponseAlias::HTTP_OK,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Task')
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $tasks = TaskResource::collection($this->taskService->list());

        return response()->json(
            [
                'success' => true,
                'data' => $tasks->resolve(),
            ],
            ResponseAlias::HTTP_OK
        );
    }

    #[OA\Post(
        path: '/api/tasks',
        summary: 'Создать задачу',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TaskPayload')
        ),
        tags: ['Tasks'],
        responses: [
            new OA\Response(
                response: ResponseAlias::HTTP_CREATED,
                description: 'Задача создана',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Ошибки валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string')
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function store(TaskRequest $request): JsonResponse
    {
        try {
            $task = new TaskResource($this->taskService->create($request->toDto()));
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                $e->getCode()
            );
        }

        return response()->json(
            [
                'success' => true,
                'data' => $task->resolve(),
            ],
            ResponseAlias::HTTP_CREATED
        );
    }

    #[OA\Get(
        path: '/api/tasks/{id}',
        summary: 'Просмотреть задачу',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: ResponseAlias::HTTP_OK,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: ResponseAlias::HTTP_NOT_FOUND,
                description: 'Задача не найдена'
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $task = new TaskResource($this->taskService->show($id));
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                $e->getCode()
            );
        }

        return response()->json(
            [
                'success' => true,
                'data' => $task->resolve(),
            ],
            ResponseAlias::HTTP_OK
        );
    }

    #[OA\Put(
        path: '/api/tasks/{id}',
        summary: 'Обновить задачу',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TaskPayload')
        ),
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: ResponseAlias::HTTP_OK,
                description: 'Задача обновлена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: ResponseAlias::HTTP_NOT_FOUND,
                description: 'Задача не найдена'
            ),
            new OA\Response(
                response: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Ошибки валидации'
            ),
        ]
    )]
    public function update(TaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = new TaskResource($this->taskService->update($id, $request->toDto()));
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                $e->getCode()
            );
        }

        return response()->json(
            [
                'success' => true,
                'data' => $task->resolve(),
            ],
            ResponseAlias::HTTP_OK
        );
    }

    #[OA\Delete(
        path: '/api/tasks/{id}',
        summary: 'Удалить задачу',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: ResponseAlias::HTTP_NO_CONTENT,
                description: 'Задача удалена'
            ),
            new OA\Response(
                response: ResponseAlias::HTTP_NOT_FOUND,
                description: 'Задача не найдена'
            ),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->taskService->delete($id);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                $e->getCode()
            );
        }

        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
