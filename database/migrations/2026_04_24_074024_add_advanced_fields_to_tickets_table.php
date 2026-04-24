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
        Schema::table('tickets', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique();
            $table->dateTime('due_date')->nullable();
            $table->integer('rating')->nullable();
            $table->text('feedback_comment')->nullable();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn(['uuid', 'due_date', 'rating', 'feedback_comment', 'asset_id']);
        });
    }
};
