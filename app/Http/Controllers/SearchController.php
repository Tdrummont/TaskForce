<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Global search across all resources
     */
    public function globalSearch(Request $request)
    {
        $query = $request->query('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'tasks' => [],
                'users' => [],
                'messages' => []
            ]);
        }

        try {
            $userId = Auth::id();
            $searchTerm = "%{$query}%";

            // Search tasks
            $tasks = Task::where('created_by', $userId)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                })
                ->with(['assignedTo'])
                ->select('id', 'title', 'description', 'status', 'priority', 'category', 'created_at', 'assigned_to')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'category' => $task->category,
                        'created_at' => $task->created_at,
                        'assigned_to' => $task->assignedTo ? [
                            'id' => $task->assignedTo->id,
                            'name' => $task->assignedTo->name,
                            'email' => $task->assignedTo->email,
                        ] : null
                    ];
                });

            // Search users
            $users = User::where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm);
                })
                ->select('id', 'name', 'email')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                });

            // Search task comments (messages)
            $messages = TaskComment::whereHas('task', function ($q) use ($userId) {
                    $q->where('created_by', $userId);
                })
                ->where('content', 'like', $searchTerm)
                ->with(['task'])
                ->select('id', 'task_id', 'content', 'created_at')
                ->limit(5)
                ->get()
                ->map(fn ($msg) => [
                    'id' => $msg->id,
                    'title' => "Comentário em: {$msg->task->title}",
                    'content' => $msg->content,
                    'created_at' => $msg->created_at,
                    'task_id' => $msg->task_id,
                ]);

            return response()->json([
                'tasks' => $tasks,
                'users' => $users,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json([
                'tasks' => [],
                'users' => [],
                'messages' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Full search results page
     */
    public function results(Request $request)
    {
        $query = $request->query('q', '');
        $tab = $request->query('tab', 'tasks');
        
        if (strlen($query) < 2) {
            return redirect()->route('tasks.index');
        }

        $userId = Auth::id();
        $searchTerm = "%{$query}%";

        // Search based on tab
        $results = match ($tab) {
            'users' => $this->searchUsers($userId, $searchTerm),
            'messages' => $this->searchMessages($userId, $searchTerm),
            default => $this->searchTasks($userId, $searchTerm),
        };

        return inertia('Search/Results', [
            'query' => $query,
            'activeTab' => $tab,
            'results' => $results,
            'totalCount' => count($results),
        ]);
    }

    /**
     * Search tasks
     */
    private function searchTasks($userId, $searchTerm)
    {
        return Task::where('created_by', $userId)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhereJsonContains('tags', $searchTerm);
            })
            ->with(['assignedTo', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    /**
     * Search users
     */
    private function searchUsers($userId, $searchTerm)
    {
        return User::where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            })
            ->whereHas('assignedTasks', function ($q) use ($userId) {
                $q->where('created_by', $userId);
            })
            ->withCount(['assignedTasks' => function ($q) use ($userId) {
                $q->where('created_by', $userId);
            }])
            ->orderBy('name')
            ->paginate(20);
    }

    /**
     * Search messages (task comments)
     */
    private function searchMessages($userId, $searchTerm)
    {
        return TaskComment::whereHas('task', function ($q) use ($userId) {
                $q->where('created_by', $userId);
            })
            ->where('content', 'like', $searchTerm)
            ->with(['task', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
