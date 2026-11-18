<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('markers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('type_id')->nullable();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('address', 500)->default('');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('folder_path', 500)->nullable();
            $table->char('source_key', 40)->nullable()->unique();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('marker_types')->nullOnDelete();
            $table->index(['latitude', 'longitude'], 'markers_coordinates_index');
            $table->index('type_id', 'markers_type_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markers');
    }
};
