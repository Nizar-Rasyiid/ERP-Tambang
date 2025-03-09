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
        Schema::create('detailinvoices', function (Blueprint $table) {
            $table->id('id_detail_invoice');

            $table->foreignId('id_invoice')
                ->references('id_invoice')
                ->on('invoices')
                ->onDelete('cascade');  
            
            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');

            $table->foreignId('id_do')
                ->references('id_do')
                ->on('deliveryorders')
                ->onDelete('cascade');
            
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailinvoices');
    }
};
