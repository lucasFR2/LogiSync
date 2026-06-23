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
        // 1. users Table (Funcionários)
        Schema::table('users', function (Blueprint $table) {
            $table->date('admission_date')->nullable()->after('rg');
            $table->string('complement')->nullable()->after('number');
        });

        // 2. customers Table (Clientes)
        Schema::table('customers', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('state_registration');
            $table->string('gender')->nullable()->after('birth_date');
            $table->string('complement')->nullable()->after('number');
        });

        // 3. suppliers Table (Fornecedores)
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('complement')->nullable()->after('number');
        });

        // 4. invoices Table (Notas Fiscais)
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('conference_status')->default('Pendente')->after('status');
            $table->unsignedBigInteger('conferred_by')->nullable()->after('conference_status');
            $table->dateTime('conferred_at')->nullable()->after('conferred_by');
            $table->text('conference_notes')->nullable()->after('conferred_at');

            $table->foreign('conferred_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['conferred_by']);
            $table->dropColumn(['conference_status', 'conferred_by', 'conferred_at', 'conference_notes']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['complement']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'gender', 'complement']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admission_date', 'complement']);
        });
    }
};
