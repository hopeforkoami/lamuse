<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

\App\Config\Database::init();

use App\Models\User;

$superAdminEmail = 'admin@example.com';
$superAdminPassword = password_hash('admin123', PASSWORD_BCRYPT);

User::updateOrCreate(
    ['email' => $superAdminEmail],
    [
        'name' => 'Super Admin',
        'password' => $superAdminPassword,
        'role' => 'super_admin'
    ]
);

echo "Seed finished successfully. Super Admin created.\n";
