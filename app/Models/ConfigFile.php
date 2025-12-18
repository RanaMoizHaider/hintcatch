<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigFile extends Model
{
    /** @use HasFactory<\Database\Factories\ConfigFileFactory> */
    use HasFactory;

    protected $fillable = [
        'config_id',
        'filename',
        'path',
        'content',
        'language',
        'is_primary',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Config, $this>
     */
    public function config(): BelongsTo
    {
        return $this->belongsTo(Config::class);
    }

    /**
     * Get the full path (path/filename).
     *
     * @return Attribute<string, never>
     */
    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->path ? "{$this->path}/{$this->filename}" : $this->filename,
        );
    }
}
