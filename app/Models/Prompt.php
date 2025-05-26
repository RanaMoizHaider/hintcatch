<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Spatie\Tags\HasTags;

class Prompt extends Model implements Viewable
{
    /** @use HasFactory<\Database\Factories\PromptFactory> */
    use HasFactory, InteractsWithViews, HasTags;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
