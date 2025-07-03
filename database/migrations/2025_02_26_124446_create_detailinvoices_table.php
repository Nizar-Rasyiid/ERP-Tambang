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

            $table->foreignId('id_so')
                ->nullable()
                ->constrained('salesorders', 'id_so')
                ->onDelete('cascade');

            $table->foreignId('id_detail_so')
                ->nullable()
                ->constrained('detailso', 'id_detail_so')
                ->onDelete('cascade');
            
            $table->foreignId('id_detail_do')
                ->nullable()            
                ->constrained('detail_do', 'id_detail_do')
                ->onDelete('cascade');
            
            $table->foreignId('id_detail_po')
                ->nullable()
                ->constrained('detailpo', 'id_detail_po')
                ->onDelete('cascade');                        
            
            $table->foreignId('id_do')
                ->references('id_do')
                ->on('deliveryorders')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');            
                    
            $table->integer('quantity')->default(0);
            $table->decimal('price', 12, 2);
            $table->decimal('amount', 12, 2);
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
