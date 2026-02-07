<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = config('app.super_admin_email');
$password = config('app.super_admin_password');

if (empty($email) || empty($password)) {
    echo "MISSING";
    exit(1);
}

$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    $user = App\Models\User::create([
        'name' => 'Super Admin',
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => $email,
        'password' => Illuminate\Support\Facades\Hash::make($password),
        'role' => 'admin',
        'is_active' => true,
    ]);
} else {
    $user->forceFill([
        'password' => Illuminate\Support\Facades\Hash::make($password),
        'role' => 'admin',
    ])->save();
}

echo "OK";
