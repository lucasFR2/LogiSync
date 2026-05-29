<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incoming_invoice_items', function (Blueprint $table) {
            // Adicionar colunas de impostos padrão
            $table->decimal('iss_rate', 5, 2)->default(0)->after('total_price');
            $table->decimal('pis_rate', 5, 2)->default(0)->after('iss_rate');
            $table->decimal('cofins_rate', 5, 2)->default(0)->after('pis_rate');
            $table->decimal('csll_rate', 5, 2)->default(0)->after('cofins_rate');
            $table->decimal('irpj_rate', 5, 2)->default(0)->after('csll_rate');
            $table->decimal('cpp_rate', 5, 2)->default(0)->after('irpj_rate');
            $table->decimal('ipi_rate', 5, 2)->default(0)->after('cpp_rate');
            
            $table->decimal('icms_rate', 5, 2)->default(0)->after('ipi_rate');
            $table->string('icms_cst')->nullable()->after('icms_rate');
            $table->integer('icms_orig')->default(0)->after('icms_cst');
            
            $table->decimal('icms_st_rate', 5, 2)->default(0)->after('icms_orig');
            $table->decimal('icms_st_mva', 5, 2)->default(0)->after('icms_st_rate');
            $table->string('icms_st_cst')->nullable()->after('icms_st_mva');
            
            $table->decimal('ibs_rate', 5, 2)->default(0)->after('icms_st_cst');
            $table->decimal('cbs_rate', 5, 2)->default(0)->after('ibs_rate');
            $table->decimal('is_rate', 5, 2)->default(0)->after('cbs_rate');
            
            $table->decimal('icms_red_bc', 5, 2)->default(0)->after('is_rate');
            $table->integer('icms_mod_bc')->default(3)->after('icms_red_bc');
        });
    }

    public function down(): void
    {
        Schema::table('incoming_invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'iss_rate', 'pis_rate', 'cofins_rate', 'csll_rate', 'irpj_rate', 'cpp_rate', 'ipi_rate',
                'icms_rate', 'icms_cst', 'icms_orig', 'icms_st_rate', 'icms_st_mva', 'icms_st_cst',
                'ibs_rate', 'cbs_rate', 'is_rate', 'icms_red_bc', 'icms_mod_bc'
            ]);
        });
    }
};
