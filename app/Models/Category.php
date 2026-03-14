<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasSlug, LogsActivity;
    protected $fillable = ['slug', 'name', 'description', 'image', 'parent_id', 'order', 'is_published', 'created_by', 'updated_by'];
    protected $appends = ['image_path', 'is_published_desc'];

    public function getIsPublishedDescAttribute()
    {
        return $this->is_published == 1 ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-danger">Not Published</span>';
    }

    public function getImagePathAttribute()
    {
        return $this->image ? asset('storage/' . Config::get('file_paths')['CATEGORY_IMAGE_PATH'] . '/' . $this->image) : null;
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children')->orderBy('order');
    }

    public static function getParentCategories()
    {
        return Category::where('parent_id', null)->get();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getAllParents()
    {
        $parents = collect();

        $parent = $this->parent;
        while ($parent) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents->reverse();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')  // Field to generate slug from
            ->saveSlugsTo('slug')         // Field to save slug into
            ->doNotGenerateSlugsOnUpdate(); // Optional: keeps original slug when updating title
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Category')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Category model has been {$eventName}");
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
