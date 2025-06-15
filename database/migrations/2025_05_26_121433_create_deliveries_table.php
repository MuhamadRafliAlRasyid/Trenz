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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('courier_id')->constrained('users')->onDelete('cascade');
            $table->enum('courier_type', ['internal', 'external'])->default('internal');
            $table->string('external_service')->nullable();
            $table->integer('external_cost')->nullable();
            $table->string('courier_name')->nullable();
            $table->string('courier_service')->nullable();
            $table->integer('shipping_cost')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('status', ['in_progress', 'done'])->default('in_progress');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
