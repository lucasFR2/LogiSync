<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('invoice_items', 'checked_quantity')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('checked_quantity', 12, 4)->default(0)->after('quantity');
            });
        }

        if (!Schema::hasColumn('incoming_invoice_items', 'checked_quantity')) {
            Schema::table('incoming_invoice_items', function (Blueprint $table) {
                $table->decimal('checked_quantity', 12, 4)->default(0)->after('quantity');
            });
        }

        // Add conference fields to incoming_invoices if not present
        if (!Schema::hasColumn('incoming_invoices', 'conference_status')) {
            Schema::table('incoming_invoices', function (Blueprint $table) {
                $table->string('conference_status')->default('Pendente')->after('entry_status');
                $table->text('conference_notes')->nullable()->after('conference_status');
                $table->unsignedBigInteger('conferred_by')->nullable()->after('conference_notes');
                $table->timestamp('conferred_at')->nullable()->after('conferred_by');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoice_items', 'checked_quantity')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('checked_quantity');
            });
        }

        if (Schema::hasColumn('incoming_invoice_items', 'checked_quantity')) {
            Schema::table('incoming_invoice_items', function (Blueprint $table) {
                $table->dropColumn('checked_quantity');
            });
        }

        if (Schema::hasColumn('incoming_invoices', 'conference_status')) {
            Schema::table('incoming_invoices', function (Blueprint $table) {
                $table->dropColumn(['conference_status', 'conference_notes', 'conferred_by', 'conferred_at']);
            });
        }
    }
};
