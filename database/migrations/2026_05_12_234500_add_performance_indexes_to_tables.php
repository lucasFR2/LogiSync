<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * High-performance indexes for search and filtering.
     */
    public function up(): void
    {
        // Indexes for products, activity_logs, suppliers and customers are already handled 
        // in 2026_05_12_215012_add_optimization_indexes_to_tables.php

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('type');
            $table->index('recipient_name');
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status', 'type', 'recipient_name', 'issued_at']);
        });
    }
};
