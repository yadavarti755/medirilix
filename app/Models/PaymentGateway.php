<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Config;

class PaymentGateway extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'payment_gateways';

    protected $fillable = [
        'gateway_name',
        'app_id',
        'client_id_or_key',
        'client_secret',
        'image',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * Spatie activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('payment_gateway')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "PaymentGateway model has been {$eventName}");
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFileUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . Config::get('file_paths.PAYMENT_GATEWAY_IMAGE_PATH') . '/' . $this->image);
        }
        return asset('assets/images/no-image.jpg');
    }
}
