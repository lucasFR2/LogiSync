<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasRole = Schema::hasColumn('users', 'role');
        $hasCpf = Schema::hasColumn('users', 'cpf');

        Schema::table('users', function (Blueprint $table) use ($hasRole, $hasCpf) {
            if (!$hasRole) {
                $table->string('role')->nullable()->after('password');
            }

            if (!$hasCpf) {
                $table->string('cpf', 14)->unique()->after('role');
            }
        });
    }

    public function down(): void
    {
        $hasCpf = Schema::hasColumn('users', 'cpf');
        $hasRole = Schema::hasColumn('users', 'role');

        Schema::table('users', function (Blueprint $table) use ($hasCpf, $hasRole) {
            if ($hasCpf) {
                $table->dropUnique(['cpf']);
                $table->dropColumn('cpf');
            }

            if ($hasRole) {
                $table->dropColumn('role');
            }
        });
    }
};
