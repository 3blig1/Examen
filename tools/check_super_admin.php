<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exists = App\Models\User::where('email', config('app.super_admin_email'))->exists();

echo $exists ? 'YES' : 'NO';
