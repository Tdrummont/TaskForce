<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:255'
        ]);

        $file = $request->file('file');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('attachments', $filename, 'public');

        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'attachment' => $attachment->load('user'),
            'message' => 'Anexo adicionado com sucesso!'
        ]);
    }

    public function download(TaskAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_filename
        );
    }

    public function destroy(TaskAttachment $attachment)
    {
        if ($attachment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Você não tem permissão para excluir este anexo');
        }

        $attachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anexo excluído com sucesso!'
        ]);
    }

    public function index(Task $task)
    {
        $attachments = $task->attachments()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'attachments' => $attachments
        ]);
    }
}
