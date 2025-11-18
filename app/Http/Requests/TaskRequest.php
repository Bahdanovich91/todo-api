<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\TaskDto;
use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

#[OA\Schema(
    schema: 'TaskPayload',
    required: ['title'],
    properties: [
        new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Call the client'),
        new OA\Property(property: 'description', type: 'string', example: 'Call tomorrow', nullable: true),
        new OA\Property(property: 'status', type: 'string', enum: TaskStatus::class, example: TaskStatus::PENDING->value),
    ]
)]
class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(TaskStatus::toArray())],
        ];
    }

    public function toDto(): TaskDto
    {
        return TaskDto::fromArray($this->validated());
    }

    /**
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = new JsonResponse([
            'success' => false,
            'errors' => $validator->errors(),
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        throw new ValidationException($validator, $response);
    }
}
