<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up($schema)
    {
        $schema->create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('sales_report');
            $table->string('s3_key');
            $table->timestamps();
        });
    }

    public function down($schema)
    {
        $schema->dropIfExists('reports');
    }
};
