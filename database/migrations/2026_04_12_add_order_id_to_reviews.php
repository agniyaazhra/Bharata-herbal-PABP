<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reviews', 'order_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade')->after('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeignIdFor('Order');
            $table->dropColumn('order_id');
        });
    }
};
