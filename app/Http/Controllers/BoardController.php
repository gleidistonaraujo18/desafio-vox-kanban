<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoardRegisterRequest;
use App\Models\Board;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        return view('boards.index');
    }

    public function indexJson()
    {
        $this->authorize('viewAny', Board::class);

        $boards = auth()->user()
            ->boards()
            ->select('id', 'name')
            ->latest()
            ->get();

        return response()->json($boards);
    }

    public function create()
    {
        return view('boards.create');
    }

    public function store(BoardRegisterRequest $request)
    {
        $this->authorize('create', Board::class);

        $board = $this->createBoard($request->name);

        if (!$board) {
            return redirect()->route('boards.index')
                ->with('error', 'Erro ao criar o quadro!');
        }


        return redirect()->route('boards.index')
            ->with('success', 'Quadro criado com sucesso!');
    }

    public function storeJson(BoardRegisterRequest $request): JsonResponse
    {
        $this->authorize('create', Board::class);

        $board = $this->createBoard($request->name);

        return response()->json($board, 201);
    }

    public function update(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255']
        ]);

        $board->update($data);

        return response()->json($board);
    }

    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return response()->json(['ok' => true]);
    }

    private function createBoard(string $name): Board
    {
        return Board::query()->create([
            'name' => $name,
            'user_id' => auth()->id(),
        ]);
    }
}
