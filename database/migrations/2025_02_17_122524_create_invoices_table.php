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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('id_invoice');
            
            $table->foreignId('id_do')
                ->nullable()
                ->references('id_do')
                ->on('deliveryorders')
                ->onDelete('cascade');
        
            $table->foreignId('id_po')
                ->references('id_po')
                ->on('purchaseorders')
                ->onDelete('cascade');
        
            $table->foreignId('id_customer')
                ->references('id_customer')
                ->on('customers')
                ->onDelete('cascade');
        
            $table->foreignId('id_bank_account')
                ->references('id_bank_account')
                ->on('bank_accounts')
                ->onDelete('cascade');
        
            $table->foreignId('id_payment_type')
                ->references('id_payment_type')
                ->on('payment_types')
                ->onDelete('cascade');
        
            $table->string('no_invoice');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
