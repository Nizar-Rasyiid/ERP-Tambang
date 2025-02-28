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
        Schema::create('detailso', function (Blueprint $table) {
            $table->id('id_detail_so');
            $table->foreignId('id_so')->constrained('salesorders', 'id_so')->onDelete('cascade');
            $table->foreignId('id_product')->constrained('products', 'id_product')->onDelete('cascade');
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
        Schema::dropIfExists('detailso');
    }
};
