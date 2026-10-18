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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 60);
            $table->string('category_en', 60)->nullable();
            $table->text('description');
            $table->text('description_en')->nullable();
            $table->json('tags');
            $table->json('tags_en')->nullable();
            $table->string('image_path');
            $table->string('icon', 20);
            $table->string('tone', 20);
            $table->boolean('is_demo')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
