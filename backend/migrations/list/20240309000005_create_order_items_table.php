<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down($schema)
    {
        $schema->dropIfExists('order_items');
    }
};
