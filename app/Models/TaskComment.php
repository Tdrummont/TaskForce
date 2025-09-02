<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'content',
        'mentions',
        'is_pinned'
    ];

    protected $casts = [
        'mentions' => 'array',
        'is_pinned' => 'boolean',
    ];

    // Relacionamentos
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentionedUsers()
    {
        return $this->belongsToMany(User::class, 'task_comment_mentions', 'comment_id', 'user_id');
    }

    // Acessors
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getFormattedContentAttribute()
    {
        $content = $this->content;
        
        // Processar menções
        if ($this->mentions) {
            foreach ($this->mentions as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $content = str_replace(
                        "@{$user->name}",
                        "<span class='bg-blue-100 text-blue-800 px-1 rounded text-sm'>@{$user->name}</span>",
                        $content
                    );
                }
            }
        }
        
        // Processar links
        $content = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" class="text-blue-600 hover:underline">$1</a>',
            $content
        );
        
        // Processar quebras de linha
        $content = nl2br($content);
        
        return $content;
    }

    public function getIsEditedAttribute()
    {
        return $this->created_at->ne($this->updated_at);
    }

    // Scopes
    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeWithMentions($query, $userId)
    {
        return $query->whereJsonContains('mentions', $userId);
    }

    // Métodos
    public function togglePin()
    {
        $this->update(['is_pinned' => !$this->is_pinned]);
        return $this->is_pinned;
    }

    public function addMention($userId)
    {
        $mentions = $this->mentions ?? [];
        if (!in_array($userId, $mentions)) {
            $mentions[] = $userId;
            $this->update(['mentions' => $mentions]);
        }
    }

    public function removeMention($userId)
    {
        $mentions = $this->mentions ?? [];
        $mentions = array_diff($mentions, [$userId]);
        $this->update(['mentions' => array_values($mentions)]);
    }

    public function hasMention($userId)
    {
        return in_array($userId, $this->mentions ?? []);
    }

    public function canEdit($user)
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }

    public function canDelete($user)
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }
}
