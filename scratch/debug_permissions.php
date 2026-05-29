<?php

use App\Models\Permission;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$permissions = Permission::select('id', 'name', 'label', 'group')
    ->orderBy('group')
    ->orderBy('label')
    ->get()
    ->groupBy('group');

foreach ($permissions as $group => $groupPermissions) {
    echo "Group: " . $group . " (type: " . gettype($groupPermissions) . ")\n";
    if (!is_iterable($groupPermissions)) {
        echo "ERROR: Group " . $group . " is not iterable!\n";
    }
}
