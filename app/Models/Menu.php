<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Menu extends Model
{
    protected $fillable = ['location', 'title', 'url', 'parent_id', 'order', 'permission_name', 'created_by', 'updated_by'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->with('children')->orderBy('order');
    }

    public function page()
    {
        return $this->hasOne(Page::class, 'menu_id');
    }

    public static function getParentMenus()
    {
        return Menu::where('parent_id', null)->get();
    }

    public static function getHeaderParentMenus()
    {
        return Menu::where([
            'parent_id' => null,
            'location' => 'header',
        ])->orderBy('order', 'ASC')->get();
    }

    public static function getAllParentMenus()
    {
        return Menu::where([
            'parent_id' => null,
        ])->whereIn('location', ['header', 'footer'])->orderBy('order', 'ASC')->get();
    }

    public static function getFooterParentMenus()
    {
        return Menu::where([
            'parent_id' => null,
            'location' => 'footer',
        ])->orderBy('order', 'ASC')->get();
    }

    public static function getQuickLinksMenus()
    {
        return Menu::where([
            'parent_id' => null,
            'location' => 'quick_links',
        ])->orderBy('order', 'ASC')->get();
    }

    public static function getInformationMenus()
    {
        return Menu::where([
            'parent_id' => null,
            'location' => 'information',
        ])->orderBy('order', 'ASC')->get();
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function getAllParents()
    {
        $parents = collect();

        $parent = $this->parent;
        while ($parent) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents->reverse(); // So it goes from top-level to immediate parent
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('menu')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Menu model has been {$eventName}");
    }
}
