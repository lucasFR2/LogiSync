<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Vínculo com transportadora cadastrada
            $table->foreignId('carrier_id')->nullable()->after('supplier_id')
                  ->constrained('carriers')->nullOnDelete();

            // Dados da transportadora (preenchidos manualmente ou auto-preenchidos)
            $table->string('carrier_name')->nullable()->after('carrier_id');
            $table->string('carrier_cnpj')->nullable()->after('carrier_name');
            $table->string('carrier_state_reg')->nullable()->after('carrier_cnpj');
            $table->string('carrier_address')->nullable()->after('carrier_state_reg');
            $table->string('carrier_city')->nullable()->after('carrier_address');
            $table->string('carrier_state', 2)->nullable()->after('carrier_city');

            // Dados do frete
            $table->string('cargo_type')->nullable()->after('carrier_state')
                  ->comment('Tipo da carga (ex: Fracionado, Carga Geral, Perigosa)');
            $table->enum('freight_account', ['0', '1', '2', '3', '9'])
                  ->default('0')->after('cargo_type')
                  ->comment('0=Emitente, 1=Destinatário, 2=Terceiros, 3=Remetente, 9=Sem Frete');
            $table->string('vehicle_plate')->nullable()->after('freight_account');
            $table->string('vehicle_uf', 2)->nullable()->after('vehicle_plate');

            // Volumes transportados
            $table->unsignedInteger('vol_quantity')->nullable()->after('vehicle_uf');
            $table->string('vol_species')->nullable()->after('vol_quantity')
                  ->comment('Espécie (ex: VOLUMES, CAIXAS, SACAS)');
            $table->string('vol_brand')->nullable()->after('vol_species');
            $table->string('vol_number')->nullable()->after('vol_brand');
            $table->decimal('vol_gross_weight', 10, 3)->nullable()->after('vol_number');
            $table->decimal('vol_net_weight', 10, 3)->nullable()->after('vol_gross_weight');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['carrier_id']);
            $table->dropColumn([
                'carrier_id', 'carrier_name', 'carrier_cnpj', 'carrier_state_reg',
                'carrier_address', 'carrier_city', 'carrier_state',
                'cargo_type', 'freight_account', 'vehicle_plate', 'vehicle_uf',
                'vol_quantity', 'vol_species', 'vol_brand', 'vol_number',
                'vol_gross_weight', 'vol_net_weight',
            ]);
        });
    }
};
