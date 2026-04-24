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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('type')->nullable(); // Printer, PC, Monitor
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Vladelets
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('status')->default('active'); // active, maintenance, retired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
