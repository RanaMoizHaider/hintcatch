<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the trait
     */
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getSlugColumn()})) {
                $model->{$model->getSlugColumn()} = $model->generateUniqueSlug();
            }
        });

        static::updating(function ($model) {
            if ($model->shouldRegenerateSlug()) {
                $model->{$model->getSlugColumn()} = $model->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the model
     */
    public function generateUniqueSlug(): string
    {
        $sourceText = $this->getSlugSource();
        $baseSlug = Str::slug($sourceText);
        
        // If the base slug is empty, use a fallback
        if (empty($baseSlug)) {
            $baseSlug = 'item-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;

        // Keep checking until we find a unique slug
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where($this->getSlugColumn(), $slug);
        
        // If updating an existing model, exclude the current model from the check
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        return $query->exists();
    }

    /**
     * Get the column name for the slug
     */
    public function getSlugColumn(): string
    {
        return property_exists($this, 'slugColumn') ? $this->slugColumn : 'slug';
    }

    /**
     * Get the source text for generating the slug
     */
    public function getSlugSource(): string
    {
        $sourceField = property_exists($this, 'slugSource') ? $this->slugSource : 'title';
        return $this->{$sourceField} ?? '';
    }

    /**
     * Determine if the slug should be regenerated on update
     */
    protected function shouldRegenerateSlug(): bool
    {
        // Only regenerate if the source field has changed and slug is empty
        $sourceField = $this->getSlugSource();
        return $this->isDirty($this->getSlugSource()) && empty($this->getOriginal($this->getSlugColumn()));
    }

    /**
     * Get the route key name for route model binding
     */
    public function getRouteKeyName(): string
    {
        return $this->getSlugColumn();
    }
}
