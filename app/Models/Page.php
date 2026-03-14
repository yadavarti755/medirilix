<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasSlug, LogsActivity;

    protected $fillable = [
        'menu_id',
        'slug',
        'title',
        'content',
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['is_published_desc'];

    public function getIsPublishedDescAttribute()
    {
        return $this->is_published ? 'Published' : 'Draft';
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')  // Field to generate slug from
            ->saveSlugsTo('slug')         // Field to save slug into
            ->doNotGenerateSlugsOnUpdate(); // Optional: keeps original slug when updating title
    }




    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('page')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Page model has been {$eventName}");
    }
}
