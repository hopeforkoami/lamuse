<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Null for guests
            $table->string('email')->nullable(); // For guest lookup
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('access_token')->nullable(); // Unique token for guest access
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down($schema)
    {
        $schema->dropIfExists('entitlements');
    }
};
