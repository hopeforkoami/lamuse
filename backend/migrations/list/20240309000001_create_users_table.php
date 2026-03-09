<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['super_admin', 'artist', 'client'])->default('client');
            $table->string('country')->nullable();
            $table->integer('star_ranking')->default(0); // 1-5 for artists
            $table->timestamps();
        });
    }

    public function down($schema)
    {
        $schema->dropIfExists('users');
    }
};
