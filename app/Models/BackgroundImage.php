<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BackgroundImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'filename',
        'url',
        'path',
        'file_size',
        'mime_type',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // Delete file when model is deleted
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }
}
