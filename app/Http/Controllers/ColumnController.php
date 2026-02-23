<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColumnRegisterRequest;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{


    public function index(Board $board)
    {
        $this->authorize('view', $board);

        $columns = Column::query()
            ->where('board_id', $board->id)
            ->orderBy('position')
            ->get(['id', 'name', 'board_id', 'position']);

        return response()->json($columns);
    }

    public function store(ColumnRegisterRequest $request, Board $board)
    {
        $this->authorize('create', [Column::class, $board]);

        $maxPosition = Column::query()
            ->where('board_id', $board->id)
            ->max('position');

        $nextPosition = is_null($maxPosition) ? 0 : ((int) $maxPosition + 1);

        $column = Column::query()->create([
            'board_id' => $board->id,
            'name' => $request->name,
            'position' => $nextPosition,
        ]);

        return response()->json($column, 201);
    }

    public function indexWithTasks(Board $board)
    {
        $this->authorize('view', $board);

        $columns = $board->columns()
            ->with(['tasks' => fn($q) => $q->orderBy('position')])
            ->orderBy('position')
            ->get();

        return response()->json($columns);
    }

    public function update(Request $request, Column $column)
    {
        $this->authorize('update', $column);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'integer', 'min:0'],
        ]);

        $column->update($data);

        return response()->json($column);
    }

    public function destroy(Column $column)
    {
        $this->authorize('delete', $column);

        $column->delete();

        return response()->json(['ok' => true]);
    }
}
