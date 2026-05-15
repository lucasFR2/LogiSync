<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionar coluna role_id como FK nullable
            $table->unsignedBigInteger('role_id')->nullable()->after('password');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('set null');
        });

        // Popular role_id baseado na coluna role (string) existente
        $roles = DB::table('roles')->get()->keyBy('name');
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            if ($user->role && isset($roles[$user->role])) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role_id' => $roles[$user->role]->id]);
            }
        }

        // Remover coluna role (string)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password');
        });

        // Reverter role_id para string role
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if ($user->role_id) {
                $role = DB::table('roles')->find($user->role_id);
                if ($role) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['role' => $role->name]);
                }
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
