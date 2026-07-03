<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;

$user = User::where('email', 'admin@logisync.com')->first();
$role = Role::where('name', 'Administrador')->first();

if ($user && $role) {
    $user->role_id = $role->id;
    $user->save();
    echo "SUCCESS: Assigned role '{$role->name}' (ID: {$role->id}) to user '{$user->email}'.\n";
} else {
    echo "ERROR: User or Role not found.\n";
}
