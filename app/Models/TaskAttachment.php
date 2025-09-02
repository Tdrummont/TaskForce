<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'description'
    ];

    protected $casts = [
        'file_size' => 'integer',
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

    // Acessors
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileIconAttribute()
    {
        $icons = [
            'image' => '📷',
            'pdf' => '📄',
            'document' => '📝',
            'spreadsheet' => '📊',
            'presentation' => '📈',
            'archive' => '📦',
            'video' => '🎥',
            'audio' => '🎵',
            'code' => '💻',
            'default' => '📎'
        ];

        $type = $this->getFileTypeCategory();
        return $icons[$type] ?? $icons['default'];
    }

    public function getFileTypeCategoryAttribute()
    {
        $mimeType = $this->file_type;
        
        if (str_starts_with($mimeType, 'image/')) return 'image';
        if (str_starts_with($mimeType, 'application/pdf')) return 'pdf';
        if (str_starts_with($mimeType, 'text/')) return 'document';
        if (str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) return 'spreadsheet';
        if (str_contains($mimeType, 'presentation') || str_contains($mimeType, 'powerpoint')) return 'presentation';
        if (str_contains($mimeType, 'zip') || str_contains($mimeType, 'rar') || str_contains($mimeType, 'tar')) return 'archive';
        if (str_starts_with($mimeType, 'video/')) return 'video';
        if (str_starts_with($mimeType, 'audio/')) return 'audio';
        if (str_contains($mimeType, 'javascript') || str_contains($mimeType, 'php') || str_contains($mimeType, 'python')) return 'code';
        
        return 'default';
    }

    public function getDownloadUrlAttribute()
    {
        return route('tasks.attachments.download', $this->id);
    }

    public function getPreviewUrlAttribute()
    {
        if ($this->file_type_category === 'image') {
            return Storage::url($this->file_path);
        }
        return null;
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

    public function scopeByType($query, $type)
    {
        return $query->where('file_type', 'like', "%{$type}%");
    }

    // Métodos
    public function deleteFile()
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            $attachment->deleteFile();
        });
    }
}
