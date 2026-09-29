<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->json('title')->nullable()->after('name');
            $table->json('alt_text')->nullable()->after('title');
            $table->json('caption')->nullable()->after('alt_text');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['title', 'alt_text', 'caption']);
        });
    }
};
