<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Column;
use App\Models\User;

class ColumnPolicy
{
    public function viewAny(User $user, Board $board): bool
    {
        return $board->user_id === $user->id;
    }

    public function view(User $user, Column $column): bool
    {
        return $column->board->user_id === $user->id;
    }

    public function create(User $user, Board $board): bool
    {
        return $board->user_id === $user->id;
    }

    public function update(User $user, Column $column): bool
    {
        return $column->board->user_id === $user->id;
    }

    public function delete(User $user, Column $column): bool
    {
        return $column->board->user_id === $user->id;
    }
}
