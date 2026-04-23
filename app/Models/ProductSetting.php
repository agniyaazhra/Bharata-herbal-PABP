<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSetting extends Model
{
    protected $fillable = [
        'minimum_stock_alert',
        'notify_email_admin',
        'notify_dashboard_only',
        'auto_disable_when_out_of_stock',
        'alert_when_below_minimum'
    ];
}
