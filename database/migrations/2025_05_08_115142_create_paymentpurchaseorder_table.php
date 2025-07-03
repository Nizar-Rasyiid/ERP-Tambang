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
        Schema::create('paymentpurchaseorder', function (Blueprint $table) {
            $table->id('payment_po_id');
            $table->foreignId('id_po')->constrained('purchaseorders', 'id_po')->onDelete('cascade');
            $table->enum('payment_method', [
                'Transfer',
                'Cash'
            ])->default('Transfer');
            $table->text('code_paymentpo');
            $table->decimal('price', 10, 2)->default(0);
            $table->date('issue_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentpurchaseorder');
    }
};
