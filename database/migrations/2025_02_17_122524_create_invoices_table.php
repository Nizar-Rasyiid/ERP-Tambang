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
            $table->foreignId('id_so')->constrained('salesorders', 'id_so')->onDelete('cascade');                                          
        
            $table->foreignId('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->references('employee_id')
                ->on('employees')
                ->onDelete('cascade');

            $table->integer('sub_total')->nullable();
            $table->integer('total_tax')->nullable();
            $table->integer('ppn')->nullable();
            $table->integer('grand_total')->nullable();
            $table->tinyInteger('approved')->default(0);
            $table->tinyInteger('has_tandater')->default(0);
            $table->text('code_invoice');            
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
