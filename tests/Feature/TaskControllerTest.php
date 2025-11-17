<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_task_list(): void
    {
        Task::factory()->count(3)->create();

        $this->getJson(route('tasks.index'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_store_persists_task(): void
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => TaskStatus::DONE->value,
        ];

        $this->postJson(route('tasks.store'), $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', $data['title'])
            ->assertJsonPath('data.status', $data['status']);

        $this->assertDatabaseHas('tasks', [
            'title' => $data['title'],
            'status' => $data['status'],
        ]);
    }

    public function test_store_returns_validation_errors(): void
    {
        $this->postJson(route('tasks.store'), [
            'description' => 'Without title',
            'status' => 'invalid',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => ['title', 'status'],
            ]);
    }

    public function test_show_returns_single_task(): void
    {
        $task = Task::factory()->create();

        $this->getJson(route('tasks.show', $task->id))
            ->assertOk()
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', $task->title);
    }

    public function test_show_returns_not_found_for_missing_task(): void
    {
        $this->getJson(route('tasks.show', 999))
            ->assertNotFound();
    }

    public function test_update_changes_task(): void
    {
        $task = Task::factory()->pending()->create();

        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => TaskStatus::DONE->value,
        ];

        $this->putJson(route('tasks.update', $task->id), $data)
            ->assertOk()
            ->assertJsonPath('data.title', $data['title'])
            ->assertJsonPath('data.status', $data['status']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $data['title'],
            'status' => $data['status'],
        ]);
    }

    public function test_update_returns_validation_errors(): void
    {
        $task = Task::factory()->create();

        $this->patchJson(route('tasks.update', $task->id), [
            'title' => 'Without valid status',
            'status' => 'unknown',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'errors' => ['status'],
            ]);
    }

    public function test_destroy_removes_task(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson(route('tasks.destroy', $task->id))
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_returns_not_found_for_missing_task(): void
    {
        $this->deleteJson(route('tasks.destroy', 999))
            ->assertNotFound();
    }
}
