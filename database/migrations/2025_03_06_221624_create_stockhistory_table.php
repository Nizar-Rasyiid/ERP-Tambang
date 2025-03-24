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
        Schema::create('stockhistory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_detail_po')->constrained('detailpo', 'id_detail_po')->onDelete('cascade');
            $table->foreignId('id_po')->constrained('purchaseorders', 'id_po')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');           
            $table->integer('quantity');
            $table->decimal('price', 10, 2); 
            $table->integer('quantity_left'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockhistory');
    }
};
