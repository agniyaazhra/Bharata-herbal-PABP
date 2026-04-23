<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('tracking_number');
            }

            if (!Schema::hasColumn('orders', 'estimated_delivery_at')) {
                $table->timestamp('estimated_delivery_at')->nullable()->after('payment_deadline');
            }
        });

        if (!Schema::hasTable('order_tracking_updates')) {
            Schema::create('order_tracking_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->string('status');
                $table->string('location_name');
                $table->text('description')->nullable();
                $table->decimal('latitude', 10, 6)->nullable();
                $table->decimal('longitude', 10, 6)->nullable();
                $table->timestamp('tracked_at');
                $table->timestamps();

                $table->index(['order_id', 'tracked_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_tracking_updates')) {
            Schema::dropIfExists('order_tracking_updates');
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'courier_name')) {
                $table->dropColumn('courier_name');
            }

            if (Schema::hasColumn('orders', 'estimated_delivery_at')) {
                $table->dropColumn('estimated_delivery_at');
            }
        });
    }
};
