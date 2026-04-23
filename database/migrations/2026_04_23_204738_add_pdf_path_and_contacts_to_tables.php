<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->string('pdf_path')->nullable();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'contact_email']);
        });
    }
};