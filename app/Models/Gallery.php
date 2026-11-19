<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'project_images';

    protected $fillable = [
        'project_id',
        'image',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getImagePathAttribute(): ?string
    {
        return $this->attributes['image'] ?? null;
    }

    public function setImagePathAttribute(string $path): void
    {
        $this->attributes['image'] = $path;
    }
}
