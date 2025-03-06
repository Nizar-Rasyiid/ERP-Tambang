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
        Schema::create('customerpoints', function (Blueprint $table) {
            $table->id('id_customer_point');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');            
            $table->text('point');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerpoints');
    }
};
