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
        Schema::create('detail_do', function (Blueprint $table) {
            $table->id('id_detail_do');
            $table->foreignId('id_do')->constrained('deliveryorders', 'id_do')->onDelete('cascade');            
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->string('code_do');
            $table->integer('quantity');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_do');
    }
};
