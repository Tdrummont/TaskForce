<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'mentions' => 'nullable|array',
            'mentions.*' => 'exists:users,id'
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'mentions' => $request->mentions ?? []
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'message' => 'Comentário adicionado com sucesso!'
        ]);
    }

    public function update(Request $request, TaskComment $comment)
    {
        if (!$comment->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para editar este comentário');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'mentions' => 'nullable|array',
            'mentions.*' => 'exists:users,id'
        ]);

        $comment->update([
            'content' => $request->content,
            'mentions' => $request->mentions ?? []
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'message' => 'Comentário atualizado com sucesso!'
        ]);
    }

    public function destroy(TaskComment $comment)
    {
        if (!$comment->canDelete(Auth::user())) {
            abort(403, 'Você não tem permissão para excluir este comentário');
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentário excluído com sucesso!'
        ]);
    }

    public function togglePin(TaskComment $comment)
    {
        if (!$comment->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para fixar este comentário');
        }

        $isPinned = $comment->togglePin();

        return response()->json([
            'success' => true,
            'is_pinned' => $isPinned,
            'message' => $isPinned ? 'Comentário fixado!' : 'Comentário desfixado!'
        ]);
    }

    public function index(Task $task)
    {
        $comments = $task->comments()->with('user')->get();

        return response()->json([
            'comments' => $comments
        ]);
    }
}
