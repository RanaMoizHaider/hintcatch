<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'filename',
        'path',
        'content',
        'language',
        'is_primary',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->path ? "{$this->path}/{$this->filename}" : $this->filename,
        );
    }
}
