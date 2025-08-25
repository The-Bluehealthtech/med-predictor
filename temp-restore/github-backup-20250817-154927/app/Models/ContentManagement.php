<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContentManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'name',
        'file_name',
        'file_path',
        'file_url',
        'mime_type',
        'file_size',
        'alt_text',
        'description',
        'is_active',
        'is_featured',
        'sort_order',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Methods
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileTypeAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isDocument(): bool
    {
        return str_starts_with($this->mime_type, 'application/');
    }

    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }
        return false;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->isImage()) {
            // Generate thumbnail path
            $pathInfo = pathinfo($this->file_path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            
            if (Storage::exists($thumbnailPath)) {
                return Storage::url($thumbnailPath);
            }
        }
        
        return $this->file_url;
    }

    // Static methods
    public static function uploadFile($file, array $data): self
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('content/' . $data['category'], $fileName, 'public');
        
        return static::create([
            'type' => $data['type'],
            'category' => $data['category'],
            'name' => $data['name'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_url' => Storage::url($filePath),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'alt_text' => $data['alt_text'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
            'metadata' => $data['metadata'] ?? [],
            'created_by' => auth()->id(),
        ]);
    }
} 