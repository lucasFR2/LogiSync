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
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->index('name');
                $table->index('category');
                $table->index('status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index('status');
                $table->index('type');
                $table->index('recipient_name');
                $table->index('issued_at');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index('action');
                $table->index('created_at');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->index('name');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('customers', function (Blueprint $table) {
                $table->index('name');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name', 'category', 'status']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status', 'type', 'recipient_name', 'issued_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action', 'created_at']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
