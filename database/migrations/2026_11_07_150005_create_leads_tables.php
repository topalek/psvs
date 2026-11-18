<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32)->default('connection');
            $table->string('name');
            $table->string('phone', 32)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locality')->nullable();
            $table->string('street')->nullable();
            $table->string('house')->nullable();
            $table->string('object_type', 64)->nullable();
            $table->foreignId('tariff_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->text('message')->nullable();
            $table->string('source_page')->nullable();
            $table->json('utm')->nullable();
            $table->string('consent_version')->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->string('status', 32)->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['type', 'status']);
            $table->index(['assigned_to', 'status']);
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(true);
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });

        Schema::create('lead_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_status_histories');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
    }
};
