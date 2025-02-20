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
        Schema::create('deliveryorders', function (Blueprint $table) {
            $table->id('id_do');
            $table->foreignId('id_customer')->constrained('customers', 'id_customer')->onDelete('cascade');
            $table->foreignId('id_employee')->constrained('employees', 'id_employee')->onDelete('cascade');
            $table->foreignId('id_bank_account')->constrained('bank_accounts', 'id_bank_account')->onDelete('cascade');
            $table->foreignId('id_po')->constrained('purchaseorders', 'id_po')->onDelete('cascade');
            $table->date('issued_at');
            $table->date('due_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveryorders');
    }
};