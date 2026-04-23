<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_tracking')) {
            Schema::create('order_tracking', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->string('keterangan');
                $table->string('lokasi');
                $table->timestamp('created_at')->nullable();

                $table->index(['order_id', 'created_at']);
            });
        }

        if (Schema::hasTable('order_tracking_updates')) {
            $existingIds = DB::table('order_tracking')->pluck('order_id')->all();

            $legacyRows = DB::table('order_tracking_updates')
                ->when(!empty($existingIds), fn ($query) => $query->whereNotIn('order_id', $existingIds))
                ->orderBy('tracked_at')
                ->get(['order_id', 'status', 'location_name', 'description', 'tracked_at']);

            foreach ($legacyRows as $row) {
                DB::table('order_tracking')->insert([
                    'order_id' => $row->order_id,
                    'keterangan' => $row->description ?: ucfirst(str_replace('_', ' ', $row->status)),
                    'lokasi' => $row->location_name,
                    'created_at' => $row->tracked_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking');
    }
};
