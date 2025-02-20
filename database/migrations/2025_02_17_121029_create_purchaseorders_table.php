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
        Schema::create('purchaseorders', function (Blueprint $table) {
            $table->id('id_po');
            $table->foreignId('id_customer')->constrained('customers', 'id_customer')->onDelete('cascade');
            $table->foreignId('id_payment_type')->constrained('payment_types', 'id_payment_type')->onDelete('cascade');
            $table->foreignId('id_bank_account')->constrained('bank_accounts', 'id_bank_account')->onDelete('cascade');
            $table->enum('po_type', ['type1', 'type2', 'type3']);
            $table->string('status_payment');
            $table->integer('sub_total');
            $table->integer('total_tax');
            $table->integer('total_service');
            $table->integer('deposit');
            $table->integer('ppn');
            $table->integer('grand_total');
            $table->date('issue_at');
            $table->date('due_at');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseorders');
    }
};
