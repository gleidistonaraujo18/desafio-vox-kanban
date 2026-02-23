<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index(Column $column): JsonResponse
    {
        $this->authorize('viewAny', [Task::class, $column]);

        $tasks = $column->tasks()
            ->orderBy('position')
            ->get(['id', 'column_id', 'title', 'description', 'position']);

        return response()->json($tasks);
    }

    public function store(Request $request, Column $column): JsonResponse
    {
        $this->authorize('create', [Task::class, $column]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $maxPosition = $column->tasks()->max('position');
        $position = is_null($maxPosition) ? 0 : ((int) $maxPosition + 1);

        $task = $column->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'position' => $position,
        ]);

        return response()->json($task, 201);
    }

    public function move(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'to_column_id' => ['required', 'uuid', 'exists:columns,id'],
            'to_position' => ['nullable', 'integer', 'min:0'],
            'ordered_task_ids' => ['nullable', 'array'],
            'ordered_task_ids.*' => ['uuid', 'distinct'],
        ]);

        return $this->processMove($task, $data);
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'task_id' => ['required', 'uuid', 'exists:tasks,id'],
            'to_column_id' => ['required', 'uuid', 'exists:columns,id'],
            'to_position' => ['nullable', 'integer', 'min:0'],
            'ordered_task_ids' => ['nullable', 'array'],
            'ordered_task_ids.*' => ['uuid', 'distinct'],
        ]);

        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);

        return $this->processMove($task, $data);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $task->update($data);

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $columnId = $task->column_id;
        $position = $task->position;

        DB::transaction(function () use ($task, $columnId, $position) {
            $task->delete();

            Task::query()
                ->where('column_id', $columnId)
                ->where('position', '>', $position)
                ->decrement('position');
        });

        return response()->json(['ok' => true]);
    }

    private function processMove(Task $task, array $data): JsonResponse
    {
        $toColumn = Column::query()->findOrFail($data['to_column_id']);

        $this->authorize('update', $task);
        $this->authorize('create', [Task::class, $toColumn]);

        $orderedTaskIds = $data['ordered_task_ids'] ?? null;
        $toPosition = (int) ($data['to_position'] ?? 0);

        if (is_array($orderedTaskIds)) {
            $taskIndex = array_search($task->id, $orderedTaskIds, true);

            if ($taskIndex === false) {
                throw ValidationException::withMessages([
                    'ordered_task_ids' => 'A lista deve conter a task movimentada.',
                ]);
            }

            $toPosition = $taskIndex;
        }

        DB::transaction(function () use ($task, $toColumn, $toPosition, $orderedTaskIds) {
            $this->moveTaskToPosition($task, $toColumn, $toPosition);

            if (is_array($orderedTaskIds)) {
                $this->syncDestinationOrder($toColumn, $orderedTaskIds);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function moveTaskToPosition(Task $task, Column $toColumn, int $toPosition): void
    {
        $fromColumnId = $task->column_id;
        $fromPosition = (int) $task->position;

        $targetCount = (int) Task::query()->where('column_id', $toColumn->id)->count();
        $maxPosition = $fromColumnId === $toColumn->id
            ? max($targetCount - 1, 0)
            : $targetCount;

        $toPosition = max(0, min($toPosition, $maxPosition));

        if ($fromColumnId === $toColumn->id) {
            if ($toPosition === $fromPosition) {
                return;
            }

            if ($toPosition > $fromPosition) {
                Task::query()
                    ->where('column_id', $fromColumnId)
                    ->where('position', '>', $fromPosition)
                    ->where('position', '<=', $toPosition)
                    ->decrement('position');
            } else {
                Task::query()
                    ->where('column_id', $fromColumnId)
                    ->where('position', '>=', $toPosition)
                    ->where('position', '<', $fromPosition)
                    ->increment('position');
            }

            $task->update(['position' => $toPosition]);
            return;
        }

        Task::query()
            ->where('column_id', $fromColumnId)
            ->where('position', '>', $fromPosition)
            ->decrement('position');

        Task::query()
            ->where('column_id', $toColumn->id)
            ->where('position', '>=', $toPosition)
            ->increment('position');

        $task->update([
            'column_id' => $toColumn->id,
            'position' => $toPosition,
        ]);
    }

    private function syncDestinationOrder(Column $toColumn, array $orderedTaskIds): void
    {
        $currentIds = Task::query()
            ->where('column_id', $toColumn->id)
            ->orderBy('position')
            ->pluck('id')
            ->all();

        $currentIdLookup = array_flip($currentIds);
        $unknownIds = array_values(array_filter($orderedTaskIds, fn(string $id) => !isset($currentIdLookup[$id])));

        if (!empty($unknownIds)) {
            throw ValidationException::withMessages([
                'ordered_task_ids' => 'A lista contém tasks que não pertencem à coluna de destino.',
            ]);
        }

        $finalOrder = $orderedTaskIds;
        $missingIds = array_values(array_diff($currentIds, $orderedTaskIds));

        if (!empty($missingIds)) {
            $finalOrder = array_merge($finalOrder, $missingIds);
        }

        foreach ($finalOrder as $position => $taskId) {
            Task::query()->whereKey($taskId)->update(['position' => $position]);
        }
    }
}
