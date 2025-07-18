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
            $table->foreignId('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('cascade');
            $table->foreignId('employee_id')
                ->references('employee_id')
                ->on('employees')
                ->onDelete('cascade');
            $table->text('code_invoice');
            $table->enum('status_payment',[
                'unpaid',
                'partial',
                'full'
            ])->default('unpaid');
            $table->decimal('sub_total', 12, 2);         
            $table->decimal('ppn', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->boolean('approved')->default(false);
            $table->boolean('has_tandater')->default(false);            
            $table->boolean('has_faktur')->default(false);            
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
