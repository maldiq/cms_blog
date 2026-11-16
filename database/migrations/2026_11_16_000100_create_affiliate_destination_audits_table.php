<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_destination_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_destination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 20);
            $table->json('changes')->nullable();
            $table->timestamps();

            $table->index(['affiliate_destination_id', 'created_at'], 'affiliate_destination_audits_destination_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_destination_audits');
    }
};
