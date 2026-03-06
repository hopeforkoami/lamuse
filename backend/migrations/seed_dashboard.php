<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();
\App\Config\Database::init();

use App\Models\User;
use App\Models\Song;

$artist = User::where('role', 'artist')->first();
if (!$artist) {
    $artist = User::create([
        'name' => 'Artist One',
        'email' => 'artist1@example.com',
        'password' => password_hash('password', PASSWORD_BCRYPT),
        'role' => 'artist',
        'country' => 'Senegal'
    ]);
}

Song::create([
    'title' => 'Song One',
    'artist_id' => $artist->id,
    'status' => 'published',
    'price' => 200,
    'currency_code' => 'XOF',
    'genre' => 'Afrobeat',
    'created_at' => date('Y-m-d H:i:s')
]);

Song::create([
    'title' => 'Song Two',
    'artist_id' => $artist->id,
    'status' => 'published',
    'price' => 300,
    'currency_code' => 'XOF',
    'genre' => 'Jazz',
    'created_at' => date('Y-m-d H:i:s')
]);

echo "Dashboard data seeded.\n";
