<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Task',
    required: ['id', 'title', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Schedule a demo'),
        new OA\Property(property: 'description', type: 'string', example: 'Calling the client', nullable: true),
        new OA\Property(property: 'status', type: 'string', enum: TaskStatus::class, example: TaskStatus::PENDING->value),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];
}
