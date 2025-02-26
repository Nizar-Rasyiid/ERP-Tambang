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

            $table->foreignId('id_po')
                ->references('id_po')
                ->on('purchaseorders')
                ->onDelete('cascade');
        
            $table->foreignId('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->references('employee_id')
                ->on('employees')
                ->onDelete('cascade');

            $table->integer('code_invoice');
            $table->string('no_invoice');
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
        Schema::dropIfExists('invoices');
    }
};
