<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing morph columns (tokenable_type, tokenable_id) and their index
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropMorphs('tokenable');
        });

        // Re-add as UUID morphs (tokenable_type varchar, tokenable_id uuid) with composite index
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->uuidMorphs('tokenable');
        });
    }

    public function down(): void
    {
        // Revert back to bigint morphs (if you ever need to go back)
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropMorphs('tokenable');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->morphs('tokenable'); // unsignedBigInteger tokenable_id + index
        });
    }
};
