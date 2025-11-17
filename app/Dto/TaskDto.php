<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\TaskStatus;

final readonly class TaskDto
{
    public function __construct(
        public string $title,
        public ?string $description,
        public TaskStatus $status = TaskStatus::PENDING
    ) {
    }

    /**
     * Преобразует DTO в массив
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
        ];
    }

    /**
     * @param array $requestData
     * @return TaskDto
     */
    public static function fromArray(array $requestData): self
    {
        return new self(
            $requestData['title'],
            $requestData['description'] ?? null,
            isset($requestData['status'])
                ? TaskStatus::from($requestData['status'])
                : TaskStatus::PENDING,
        );
    }
}
