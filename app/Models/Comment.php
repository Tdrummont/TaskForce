<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'task_id',
        'user_id',
        'parent_id',
        'body',
        'body_html',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with(['user', 'mentions']);
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, 'comment_mentions', 'comment_id', 'user_id')
            ->withPivot('read')
            ->withTimestamps();
    }

    public static function extractMentions(string $body, int $projectId): Collection
    {
        // regex: /@([a-zA-Z0-9_]+)/
        preg_match_all('/@([a-zA-Z0-9_]+)/', $body, $matches);

        $usernames = collect($matches[1] ?? [])
            ->map(fn ($u) => Str::replace('_', ' ', $u))
            ->filter()
            ->unique()
            ->values();

        // Como o projeto é novo no banco e ainda não há model completo aqui,
        // vamos buscar por users.name (caso o editor use @name exatamente).
        // Limitação ao project seria via project_user quando implementado.
        return User::query()
            ->whereIn('name', $usernames)
            ->get();
    }

    public function canEdit(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }

    public function canDelete(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }
}

