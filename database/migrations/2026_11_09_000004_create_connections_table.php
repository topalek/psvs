<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marker_from_id')->nullable();
            $table->unsignedBigInteger('marker_to_id')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('route_coordinates')->nullable();
            $table->string('style_color', 16)->default('#e49a3e');
            $table->decimal('style_width', 5, 2)->default(3);
            $table->string('folder_path', 500)->nullable();
            $table->char('source_key', 40)->nullable()->unique();
            $table->timestamps();

            $table->foreign('marker_from_id')->references('id')->on('markers')->cascadeOnDelete();
            $table->foreign('marker_to_id')->references('id')->on('markers')->cascadeOnDelete();
            $table->foreign('type_id')->references('id')->on('connection_types')->nullOnDelete();
            $table->index('marker_from_id', 'connections_marker_from_index');
            $table->index('marker_to_id', 'connections_marker_to_index');
            $table->index('type_id', 'connections_type_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
