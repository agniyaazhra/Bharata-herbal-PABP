<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
           DB::statement('
        DELETE s1 FROM settings s1
        INNER JOIN settings s2
        WHERE s1.id > s2.id
        AND s1.group = s2.group
        AND s1.key = s2.key
    ');


    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['group', 'key']);
            $table->index('group');
        });
    }
};