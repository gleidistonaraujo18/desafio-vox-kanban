<?php

namespace App\Policies;

use App\Models\Column;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user, Column $column): bool
    {
        return $column->board->user_id === $user->id;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->column->board->user_id === $user->id;
    }

    public function create(User $user, Column $column): bool
    {
        return $column->board->user_id === $user->id;
    }

    public function update(User $user, Task $task): bool
    {
        return $task->column->board->user_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->column->board->user_id === $user->id;
    }
}
