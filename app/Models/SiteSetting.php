<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SiteSetting extends Model
{
    use SoftDeletes, LogsActivity;
    protected $guarded = [];

    protected $appends = ['header_logo_full_path', 'footer_logo_full_path', 'favicon_full_path', 'admin_panel_logo_full_path'];

    public function getHeaderLogoFullPathAttribute()
    {
        return asset('storage' . Config::get('file_paths')['SITE_HEADER_LOGO_PATH'] . '/' . $this->header_logo);
    }

    public function getFooterLogoFullPathAttribute()
    {
        return asset('storage' . Config::get('file_paths')['SITE_FOOTER_LOGO_PATH'] . '/' . $this->footer_logo);
    }

    public function getFaviconFullPathAttribute()
    {
        return asset('storage' . Config::get('file_paths')['SITE_FAVICON_PATH'] . '/' . $this->favicon);
    }

    public function getAdminPanelLogoFullPathAttribute()
    {
        return asset('storage' . Config::get('file_paths')['SITE_ADMIN_PANEL_LOGO_PATH'] . '/' . $this->admin_panel_logo);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('site_setting')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Site Setting model has been {$eventName}");
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
