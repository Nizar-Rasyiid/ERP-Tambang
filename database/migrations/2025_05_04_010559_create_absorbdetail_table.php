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
        Schema::create('absorbdetail', function (Blueprint $table) {
            $table->id('id_absorb_detail');
            $table->foreignId('opex_id')->constrained('opex', 'opex_id')->onDelete('cascade');            
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absorbdetail');
    }
};
