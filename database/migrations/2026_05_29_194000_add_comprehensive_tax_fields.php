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
        // Adicionar campos fiscais na tabela de produtos
        Schema::table('products', function (Blueprint $table) {
            $table->string('ncm')->nullable()->after('category');
            $table->string('cfop_default')->nullable()->after('ncm');
            $table->string('cest')->nullable()->after('cfop_default');
            
            // Alíquotas e enquadramentos padrão
            $table->decimal('iss_rate', 5, 2)->default(0)->after('cest');
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
        });

        // Adicionar campos fiscais na tabela de itens de nota fiscal
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('icms_cst')->nullable()->after('total');
            $table->integer('icms_orig')->default(0)->after('icms_cst');
            $table->integer('icms_mod_bc')->default(3)->after('icms_orig');
            $table->decimal('icms_red_bc', 5, 2)->default(0)->after('icms_mod_bc');

            $table->string('icms_st_cst')->nullable()->after('icms_value');
            $table->decimal('icms_st_mva', 5, 2)->default(0)->after('icms_st_cst');
            $table->decimal('icms_st_base', 15, 2)->default(0)->after('icms_st_mva');
            $table->decimal('icms_st_rate', 5, 2)->default(0)->after('icms_st_base');
            $table->decimal('icms_st_value', 15, 2)->default(0)->after('icms_st_rate');

            $table->string('ipi_cst')->nullable()->after('ipi_value');
            $table->string('ipi_enq')->nullable()->after('ipi_cst');
            $table->decimal('ipi_base', 15, 2)->default(0)->after('ipi_enq');

            $table->string('pis_cst')->nullable()->after('pis_value');
            $table->decimal('pis_base', 15, 2)->default(0)->after('pis_cst');

            $table->string('cofins_cst')->nullable()->after('cofins_value');
            $table->decimal('cofins_base', 15, 2)->default(0)->after('cofins_cst');

            // ISS
            $table->string('iss_cst')->nullable()->after('cofins_base');
            $table->decimal('iss_base', 15, 2)->default(0)->after('iss_cst');
            $table->decimal('iss_rate', 5, 2)->default(0)->after('iss_base');
            $table->decimal('iss_value', 15, 2)->default(0)->after('iss_rate');

            // CSLL
            $table->string('csll_cst')->nullable()->after('iss_value');
            $table->decimal('csll_base', 15, 2)->default(0)->after('csll_cst');
            $table->decimal('csll_rate', 5, 2)->default(0)->after('csll_base');
            $table->decimal('csll_value', 15, 2)->default(0)->after('csll_rate');

            // IRPJ
            $table->string('irpj_cst')->nullable()->after('csll_value');
            $table->decimal('irpj_base', 15, 2)->default(0)->after('irpj_cst');
            $table->decimal('irpj_rate', 5, 2)->default(0)->after('irpj_base');
            $table->decimal('irpj_value', 15, 2)->default(0)->after('irpj_rate');

            // CPP
            $table->string('cpp_cst')->nullable()->after('irpj_value');
            $table->decimal('cpp_base', 15, 2)->default(0)->after('cpp_cst');
            $table->decimal('cpp_rate', 5, 2)->default(0)->after('cpp_base');
            $table->decimal('cpp_value', 15, 2)->default(0)->after('cpp_rate');

            // Reforma 2026 (IBS, CBS, IS)
            $table->string('ibs_cst')->nullable()->after('cpp_value');
            $table->decimal('ibs_base', 15, 2)->default(0)->after('ibs_cst');
            $table->decimal('ibs_rate', 5, 2)->default(0)->after('ibs_base');
            $table->decimal('ibs_value', 15, 2)->default(0)->after('ibs_rate');

            $table->string('cbs_cst')->nullable()->after('ibs_value');
            $table->decimal('cbs_base', 15, 2)->default(0)->after('cbs_cst');
            $table->decimal('cbs_rate', 5, 2)->default(0)->after('cbs_base');
            $table->decimal('cbs_value', 15, 2)->default(0)->after('cbs_rate');

            $table->string('is_cst')->nullable()->after('cbs_value');
            $table->decimal('is_base', 15, 2)->default(0)->after('is_cst');
            $table->decimal('is_rate', 5, 2)->default(0)->after('is_base');
            $table->decimal('is_value', 15, 2)->default(0)->after('is_rate');

            // II (Importação)
            $table->decimal('ii_base', 15, 2)->default(0)->after('is_value');
            $table->decimal('ii_rate', 5, 2)->default(0)->after('ii_base');
            $table->decimal('ii_value', 15, 2)->default(0)->after('ii_rate');
            $table->decimal('ii_desp', 15, 2)->default(0)->after('ii_value');
            $table->decimal('ii_iof', 15, 2)->default(0)->after('ii_desp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ncm', 'cfop_default', 'cest', 'iss_rate', 'pis_rate', 'cofins_rate',
                'csll_rate', 'irpj_rate', 'cpp_rate', 'ipi_rate', 'icms_rate', 'icms_cst',
                'icms_orig', 'icms_st_rate', 'icms_st_mva', 'icms_st_cst', 'ibs_rate',
                'cbs_rate', 'is_rate'
            ]);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'icms_cst', 'icms_orig', 'icms_mod_bc', 'icms_red_bc',
                'icms_st_cst', 'icms_st_mva', 'icms_st_base', 'icms_st_rate', 'icms_st_value',
                'ipi_cst', 'ipi_enq', 'ipi_base', 'pis_cst', 'pis_base', 'cofins_cst', 'cofins_base',
                'iss_cst', 'iss_base', 'iss_rate', 'iss_value',
                'csll_cst', 'csll_base', 'csll_rate', 'csll_value',
                'irpj_cst', 'irpj_base', 'irpj_rate', 'irpj_value',
                'cpp_cst', 'cpp_base', 'cpp_rate', 'cpp_value',
                'ibs_cst', 'ibs_base', 'ibs_rate', 'ibs_value',
                'cbs_cst', 'cbs_base', 'cbs_rate', 'cbs_value',
                'is_cst', 'is_base', 'is_rate', 'is_value',
                'ii_base', 'ii_rate', 'ii_value', 'ii_desp', 'ii_iof'
            ]);
        });
    }
};
