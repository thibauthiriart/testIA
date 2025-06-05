<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('source')->index();
            $table->string('source_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('surface', 8, 2)->nullable();
            $table->integer('rooms')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('department')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('property_type')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('url');
            $table->json('images')->nullable();
            $table->json('additional_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('scraped_at');
            $table->timestamps();
            
            $table->unique(['source', 'source_id']);
            $table->index(['city', 'postal_code']);
            $table->index(['price', 'surface']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
