<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'sales_count')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('sales_count')->default(0)->after('rating_count');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sales_count')) {
                $table->dropColumn('sales_count');
            }
        });
    }
};
