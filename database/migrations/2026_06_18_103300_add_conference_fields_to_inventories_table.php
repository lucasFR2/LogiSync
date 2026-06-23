<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'checked_quantity')) {
                $table->integer('checked_quantity')->nullable()->after('quantity')->comment('Quantidade conferida/verificada');
            }
            if (!Schema::hasColumn('inventories', 'conference_status')) {
                $table->string('conference_status')->default('pendente')->after('status')->comment('Status da conferência: pendente, confirmada, divergente');
            }
            if (!Schema::hasColumn('inventories', 'conference_notes')) {
                $table->text('conference_notes')->nullable()->after('conference_status')->comment('Observações da conferência');
            }
        });

        // Initialize existing confirmed entries
        DB::table('inventories')
            ->where('status', 'confirmada')
            ->update([
                'checked_quantity' => DB::raw('quantity'),
                'conference_status' => 'confirmada'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['checked_quantity', 'conference_status', 'conference_notes']);
        });
    }
};
