<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login')->nullable()->after('role');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'super_admin') DEFAULT 'customer'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login');
        });
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin') DEFAULT 'customer'");
    }
};
