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
            $table->foreignId('vendor_id')
                ->constrained('vendors', 'vendor_id')
                ->onDelete('cascade');
            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->onDelete('cascade');
            $table->text('code_po');
            $table->text('termin');            
            $table->enum('status_payment', [
                'unpaid',
                'partial',
                'full'
            ])->default('unpaid');
            $table->decimal('sub_total', 12, 2);                        
            $table->decimal('deposit', 12, 2);
            $table->decimal('ppn', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->boolean('has_gr')->default(false);            
            $table->boolean('approved')->default(false);
            $table->text('desc')->nullable();
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
