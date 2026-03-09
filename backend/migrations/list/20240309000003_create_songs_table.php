<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('songs', function (Blueprint $table) {
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
    }

    public function down($schema)
    {
        $schema->dropIfExists('songs');
    }
};
