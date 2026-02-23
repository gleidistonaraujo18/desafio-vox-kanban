<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanbanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_boards_page(): void
    {
        $this->get('/boards')
            ->assertRedirect('/');
    }

    public function test_authenticated_user_can_create_board_via_api(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/boards', [
            'name' => 'Roadmap 2026',
        ]);

        $response->assertCreated()
            ->assertJsonPath('name', 'Roadmap 2026')
            ->assertJsonPath('user_id', $user->id);

        $this->assertDatabaseHas('boards', [
            'name' => 'Roadmap 2026',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_access_columns_of_board_from_other_user(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $board = Board::query()->create([
            'name' => 'Board do dono',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($intruder)
            ->getJson("/api/boards/{$board->id}/columns")
            ->assertForbidden();
    }

    public function test_task_move_endpoint_updates_column_and_order(): void
    {
        $user = User::factory()->create();
        $board = Board::query()->create([
            'name' => 'Board A',
            'user_id' => $user->id,
        ]);

        $todo = Column::query()->create([
            'board_id' => $board->id,
            'name' => 'Todo',
            'position' => 0,
        ]);

        $doing = Column::query()->create([
            'board_id' => $board->id,
            'name' => 'Doing',
            'position' => 1,
        ]);

        $taskA = Task::query()->create([
            'column_id' => $todo->id,
            'title' => 'Task A',
            'position' => 0,
        ]);

        $taskB = Task::query()->create([
            'column_id' => $todo->id,
            'title' => 'Task B',
            'position' => 1,
        ]);

        $taskC = Task::query()->create([
            'column_id' => $doing->id,
            'title' => 'Task C',
            'position' => 0,
        ]);

        $this->actingAs($user)
            ->patchJson("/api/tasks/{$taskA->id}/move", [
                'to_column_id' => $doing->id,
                'ordered_task_ids' => [$taskC->id, $taskA->id],
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $taskA->id,
            'column_id' => $doing->id,
            'position' => 1,
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $taskC->id,
            'column_id' => $doing->id,
            'position' => 0,
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $taskB->id,
            'column_id' => $todo->id,
            'position' => 0,
        ]);
    }
}
