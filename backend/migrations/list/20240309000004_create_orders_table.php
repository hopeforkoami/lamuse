<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('orders', function (Blueprint $table) {
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
    }

    public function down($schema)
    {
        $schema->dropIfExists('orders');
    }
};
