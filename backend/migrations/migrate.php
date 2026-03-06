<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$capsule = \App\Config\Database::init();
$schema = $capsule->schema();

// Disable foreign key checks for dropping tables
$capsule->getConnection()->statement('SET FOREIGN_KEY_CHECKS = 0');

// Users Table
$schema->dropIfExists('users');
$schema->create('users', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['super_admin', 'artist', 'client'])->default('client');
    $table->string('country')->nullable();
    $table->integer('star_ranking')->default(0); // 1-5 for artists
    $table->timestamps();
});

// Star Pricing Rules Table
$schema->dropIfExists('star_pricing_rules');
$schema->create('star_pricing_rules', function ($table) {
    $table->id();
    $table->integer('star_level');
    $table->string('currency_code');
    $table->decimal('min_price', 10, 2);
    $table->decimal('max_price', 10, 2);
    $table->timestamps();
});

// Songs Table
$schema->dropIfExists('songs');
$schema->create('songs', function ($table) {
    $table->id();
    $table->string('title');
    $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
    $table->enum('status', ['draft', 'published', 'upcoming', 'archived'])->default('draft');
    $table->decimal('price', 10, 2);
    $table->string('currency_code');
    $table->string('main_audio_s3_key')->nullable();
    $table->string('teaser_audio_s3_key')->nullable();
    $table->string('cover_s3_key')->nullable();
    $table->string('genre')->nullable();
    $table->string('duration')->nullable();
    $table->date('release_date')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});

// Orders Table
$schema->dropIfExists('orders');
$schema->create('orders', function ($table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('email'); // for guest checkout
    $table->decimal('total_amount', 10, 2);
    $table->string('currency_code');
    $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
    $table->string('payment_provider')->nullable();
    $table->string('payment_id')->nullable();
    $table->timestamps();
});

// Order Items Table
$schema->dropIfExists('order_items');
$schema->create('order_items', function ($table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});

// Entitlements Table (Buyer access to songs)
$schema->dropIfExists('entitlements');
$schema->create('entitlements', function ($table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Null for guests
    $table->string('email')->nullable(); // For guest lookup
    $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->string('access_token')->nullable(); // Unique token for guest access
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
});

// Payment Health Table
$schema->dropIfExists('payment_health');
$schema->create('payment_health', function ($table) {
    $table->id();
    $table->string('provider'); // paypal, cinetpay, paydunia
    $table->enum('status', ['UP', 'DOWN', 'DEGRADED'])->default('UP');
    $table->integer('latency_ms')->nullable();
    $table->timestamp('last_check_at')->useCurrent();
    $table->text('error_message')->nullable();
});

// Reports Table (Generated PDFs)
$schema->dropIfExists('reports');
$schema->create('reports', function ($table) {
    $table->id();
    $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
    $table->string('type')->default('sales_report');
    $table->string('s3_key');
    $table->timestamps();
});

// Re-enable foreign key checks
$capsule->getConnection()->statement('SET FOREIGN_KEY_CHECKS = 1');

echo "Migration finished successfully.\n";
