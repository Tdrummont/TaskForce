<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'start_date',
        'estimated_hours',
        'actual_hours',
        'category',
        'tags',
        'parent_task_id',
        'order',
        'assigned_to',
        'created_by',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'tags' => 'array',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'order' => 'integer',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id')->orderBy('order');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
    }

    public function pinnedComments()
    {
        return $this->hasMany(TaskComment::class)->where('is_pinned', true);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes para filtros
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTags($query, $tags)
    {
        if (is_array($tags)) {
            return $query->whereJsonContains('tags', $tags);
        }
        return $query->whereJsonContains('tags', [$tags]);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    public function scopeByAssignee($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())->where('status', '!=', 'completed');
    }

    public function scopeDueToday($query)
    {
        return $query->where('due_date', Carbon::today());
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays($days)]);
    }

    public function scopeParentTasks($query)
    {
        return $query->whereNull('parent_task_id');
    }

    public function scopeSubtasks($query)
    {
        return $query->whereNotNull('parent_task_id');
    }

    public function scopeWithSubtasks($query)
    {
        return $query->with(['subtasks' => function ($q) {
            $q->orderBy('order');
        }]);
    }

    public function scopeWithAttachments($query)
    {
        return $query->with('attachments');
    }

    public function scopeWithComments($query)
    {
        return $query->with(['comments' => function ($q) {
            $q->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
        }]);
    }

    // Acessors
    public function getDueStatusAttribute()
    {
        if ($this->status === 'completed') {
            return 'completed';
        }

        if ($this->isOverdue()) {
            return 'overdue';
        }

        if ($this->isDueToday()) {
            return 'due_today';
        }

        if ($this->isDueSoon()) {
            return 'due_soon';
        }

        return 'normal';
    }

    public function getDueStatusColorAttribute()
    {
        $colors = [
            'completed' => 'green',
            'overdue' => 'red',
            'due_today' => 'yellow',
            'due_soon' => 'orange',
            'normal' => 'gray'
        ];

        return $colors[$this->due_status] ?? 'gray';
    }

    public function getDueStatusLabelAttribute()
    {
        $labels = [
            'completed' => 'Concluída',
            'overdue' => 'Atrasada',
            'due_today' => 'Vence hoje',
            'due_soon' => 'Vence em breve',
            'normal' => 'No prazo'
        ];

        return $labels[$this->due_status] ?? 'Normal';
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->subtasks->count() > 0) {
            $completedSubtasks = $this->subtasks->where('status', 'completed')->count();
            return round(($completedSubtasks / $this->subtasks->count()) * 100);
        }
        return $this->status === 'completed' ? 100 : 0;
    }

    public function getEstimatedTimeFormattedAttribute()
    {
        if (!$this->estimated_hours) return null;
        
        $hours = floor($this->estimated_hours / 60);
        $minutes = $this->estimated_hours % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    public function getActualTimeFormattedAttribute()
    {
        if (!$this->actual_hours) return null;
        
        $hours = floor($this->actual_hours / 60);
        $minutes = $this->actual_hours % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    public function getTagsFormattedAttribute()
    {
        if (!$this->tags) return [];
        
        return array_map(function ($tag) {
            return [
                'name' => $tag,
                'color' => $this->getTagColor($tag)
            ];
        }, $this->tags);
    }

    // Métodos
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function isDueToday()
    {
        return $this->due_date && $this->due_date->isToday();
    }

    public function isDueSoon($days = 3)
    {
        return $this->due_date && 
               $this->due_date->isBetween(Carbon::today(), Carbon::today()->addDays($days));
    }

    public function isParent()
    {
        return is_null($this->parent_task_id);
    }

    public function isSubtask()
    {
        return !is_null($this->parent_task_id);
    }

    public function hasSubtasks()
    {
        return $this->subtasks->count() > 0;
    }

    public function hasAttachments()
    {
        return $this->attachments->count() > 0;
    }

    public function hasComments()
    {
        return $this->comments->count() > 0;
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_diff($tags, [$tag]);
        $this->update(['tags' => array_values($tags)]);
    }

    public function hasTag($tag)
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function moveToPosition($newOrder)
    {
        $this->update(['order' => $newOrder]);
    }

    public function getTagColor($tag)
    {
        $colors = [
            'urgente' => 'red',
            'importante' => 'orange',
            'bug' => 'red',
            'feature' => 'blue',
            'documentação' => 'green',
            'design' => 'purple',
            'teste' => 'yellow',
            'deploy' => 'indigo'
        ];

        return $colors[strtolower($tag)] ?? 'gray';
    }

    public function canEdit($user)
    {
        return $this->created_by === $user->id || 
               $this->assigned_to === $user->id || 
               $user->hasRole('admin');
    }

    public function canDelete($user)
    {
        return $this->created_by === $user->id || $user->hasRole('admin');
    }

    public function canAssign($user)
    {
        return $this->created_by === $user->id || $user->hasRole('admin');
    }
}
