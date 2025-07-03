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
        Schema::create('paymentsalesorder', function (Blueprint $table) {
            $table->id('payment_so_id');
            $table->foreignId('id_invoice')->constrained('invoices', 'id_invoice')->onDelete('cascade');            
            $table->enum('payment_method', ['Cash', 'Transfer'])->default('Transfer');
            $table->text('code_paymentso');
            $table->decimal('price', 10, 2);
            $table->date('issue_at')->nullable();
            $table->date('due_at')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentsalesorder');
    }
};
