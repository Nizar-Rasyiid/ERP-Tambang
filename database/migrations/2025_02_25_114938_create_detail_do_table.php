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
            $table->foreignId('id_do')
                ->constrained('deliveryorders', 'id_do')
                ->onDelete('cascade');    

            $table->foreignId('id_po')
                ->nullable()
                ->constrained('purchaseorders', 'id_po')
                ->onDelete('cascade');

            $table->foreignId('id_detail_po')
                ->nullable()
                ->constrained('detailpo', 'id_detail_po')
                ->onDelete('cascade');

            $table->foreignId('id_detail_so')
                ->nullable()
                ->constrained('detailso', 'id_detail_so')
                ->onDelete('cascade');            

            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');            
            $table->integer('quantity');
            $table->integer('quantity_left')->default(0);
            $table->decimal('price', 12, 2);
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
