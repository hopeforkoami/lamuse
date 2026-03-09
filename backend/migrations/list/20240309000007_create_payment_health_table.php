<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('payment_health', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // paypal, cinetpay, paydunia
            $table->enum('status', ['UP', 'DOWN', 'DEGRADED'])->default('UP');
            $table->integer('latency_ms')->nullable();
            $table->timestamp('last_check_at')->useCurrent();
            $table->text('error_message')->nullable();
        });
    }

    public function down($schema)
    {
        $schema->dropIfExists('payment_health');
    }
};
