<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 32)->default('locality');
            $table->string('district')->nullable();
            $table->string('verification_status', 32)->default('unverified');
            $table->timestamp('last_verified_at')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technology_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('internal_code')->nullable();
            $table->string('customer_type', 32)->default('residential');
            $table->unsignedInteger('speed_mbps')->nullable();
            $table->unsignedInteger('upload_speed_mbps')->nullable();
            $table->unsignedInteger('regular_price')->default(0)->comment('Цена в копейках');
            $table->unsignedInteger('promo_price')->nullable()->comment('Цена в копейках');
            $table->unsignedSmallInteger('price_period_days')->default(30);
            $table->unsignedInteger('price_after_promo')->nullable()->comment('Цена в копейках');
            $table->timestamp('promo_starts_at')->nullable();
            $table->timestamp('promo_ends_at')->nullable();
            $table->unsignedInteger('connection_fee')->nullable()->comment('Цена в копейках');
            $table->unsignedInteger('equipment_fee')->nullable()->comment('Цена в копейках');
            $table->text('equipment_info')->nullable();
            $table->json('included_services')->nullable();
            $table->text('terms')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->date('effective_date')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'is_active', 'customer_type']);
            $table->index(['sort_order']);
        });

        Schema::create('area_tariff', function (Blueprint $table) {
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tariff_id')->constrained()->cascadeOnDelete();
            $table->primary(['area_id', 'tariff_id']);
        });

        Schema::create('service_tariff', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tariff_id')->constrained()->cascadeOnDelete();
            $table->primary(['service_id', 'tariff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_tariff');
        Schema::dropIfExists('area_tariff');
        Schema::dropIfExists('tariffs');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('services');
        Schema::dropIfExists('technologies');
    }
};
