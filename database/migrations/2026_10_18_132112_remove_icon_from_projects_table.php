<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The icon is now derived from the category, so it no longer needs storing.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Previously chosen icons cannot be restored; every project comes back with the generic code icon.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('icon', 20)->default('code')->after('image_path');
        });
    }
};
