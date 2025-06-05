<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Les colonnes city, postal_code et department sont déjà supprimées
        // Nous devons juste rendre city_id obligatoire

        // SQLite ne supporte pas bien les changements de colonnes, utilisons SQL brut
        DB::statement('PRAGMA foreign_keys=off;');
        DB::statement('CREATE TABLE properties_new LIKE properties;');
        DB::statement('ALTER TABLE properties_new MODIFY city_id BIGINT UNSIGNED NOT NULL;');
        DB::statement('INSERT INTO properties_new SELECT * FROM properties WHERE city_id IS NOT NULL;');
        DB::statement('DROP TABLE properties;');
        DB::statement('ALTER TABLE properties_new RENAME TO properties;');
        DB::statement('PRAGMA foreign_keys=on;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Recréer les anciennes colonnes
            $table->string('city')->nullable()->after('bedrooms');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('department')->nullable()->after('postal_code');

            // Rendre city_id nullable à nouveau
            $table->unsignedBigInteger('city_id')->nullable()->change();
        });
    }
};
