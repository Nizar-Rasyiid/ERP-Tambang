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
        Schema::create('salesorders', function (Blueprint $table) {
            $table->id('id_so');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');            
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade');
            $table->text('code_so');
            $table->text('po_number');
            $table->text('termin');            
            $table->integer('total_tax');                                 
            $table->integer('sub_total');                        
            $table->integer('deposit');
            $table->integer('ppn');
            $table->integer('grand_total');
            $table->integer('has_do');
            $table->integer('has_invoice');
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
        Schema::dropIfExists('salesorders');
    }
};
