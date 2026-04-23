<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
       Schema::create('product_settings', function (Blueprint $table) {
    $table->id();

    $table->integer('minimum_stock_alert')->default(10);

    $table->boolean('notify_email_admin')->default(true);
    $table->boolean('notify_dashboard_only')->default(false);

    $table->boolean('auto_disable_when_out_of_stock')->default(true);
    $table->boolean('alert_when_below_minimum')->default(true);

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('product_settings');
    }
};
