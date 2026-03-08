<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testGetTasks(): void
    {
        Task::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function testCreateTask(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tasks', [
            'title'    => 'テストタスク',
            'due_date' => '2026-12-31',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'due_date', 'completed']);
        $this->assertDatabaseHas('tasks', [
            'title'   => 'テストタスク',
            'user_id' => $this->user->id,
        ]);
    }

    public function testUpdateTask(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}",
[
            'completed' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson(['completed' => true]);
    }

    public function testDeleteTask(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this
            ->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing(
            'tasks', [
                'id' => $task->id,
            ]
        );
    }

    public function testCannotAccessOtherUserTask(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()
            ->create(['user_id' => $otherUser->id]);

        $response = $this
            ->actingAs($this->user)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}