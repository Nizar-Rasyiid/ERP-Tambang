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
            $table->foreignId('customer_id')
                ->constrained('customers', 'customer_id')
                ->onDelete('cascade');
            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');
            $table->foreignId('id_so')
                ->constrained('salesorders', 'id_so')
                ->onDelete('cascade');
            $table->foreignId('id_customer_point')
                ->constrained('customerpoints', 'id_customer_point')->onDelete('cascade');
            $table->text('code_do');
            $table->decimal('sub_total', 12, 2);
            $table->decimal('ppn', 12, 2);
            $table->boolean('has_inv')->default(false);            
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
        Schema::dropIfExists('deliveryorders');
    }
};