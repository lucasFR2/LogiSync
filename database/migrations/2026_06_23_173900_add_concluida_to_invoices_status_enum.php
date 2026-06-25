<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('rascunho', 'emitida', 'cancelada', 'concluída') NOT NULL DEFAULT 'rascunho'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('invoices')->where('status', 'concluída')->update(['status' => 'emitida']);
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('rascunho', 'emitida', 'cancelada') NOT NULL DEFAULT 'rascunho'");
        }
    }
};
