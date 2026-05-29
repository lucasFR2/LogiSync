<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('ncm')->nullable()->after('description');
            $table->string('cfop')->nullable()->after('ncm');
            $table->decimal('icms_base', 15, 2)->default(0)->after('total');
            $table->decimal('icms_rate', 5, 2)->default(0)->after('icms_base');
            $table->decimal('icms_value', 15, 2)->default(0)->after('icms_rate');
            $table->decimal('ipi_rate', 5, 2)->default(0)->after('icms_value');
            $table->decimal('ipi_value', 15, 2)->default(0)->after('ipi_rate');
            $table->decimal('pis_rate', 5, 2)->default(0)->after('ipi_value');
            $table->decimal('pis_value', 15, 2)->default(0)->after('pis_rate');
            $table->decimal('cofins_rate', 5, 2)->default(0)->after('pis_value');
            $table->decimal('cofins_value', 15, 2)->default(0)->after('cofins_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'ncm', 'cfop', 'icms_base', 'icms_rate', 'icms_value',
                'ipi_rate', 'ipi_value', 'pis_rate', 'pis_value',
                'cofins_rate', 'cofins_value'
            ]);
        });
    }
};
