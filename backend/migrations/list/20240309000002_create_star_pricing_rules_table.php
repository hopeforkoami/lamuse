<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('star_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('star_level');
            $table->string('currency_code');
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down($schema)
    {
        $schema->dropIfExists('star_pricing_rules');
    }
};
