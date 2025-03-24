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
        Schema::create('opex', function (Blueprint $table) {
            $table->id('opex_id');
            $table->string('opex_code');
            $table->string('opex_name');
            $table->string('opex_type');
            $table->bigInteger('opex_price');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opex');
    }
};
