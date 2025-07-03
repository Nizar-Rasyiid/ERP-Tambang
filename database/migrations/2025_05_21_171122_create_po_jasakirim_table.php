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
        Schema::create('po_jasakirim', function (Blueprint $table) {
            $table->id('id_jasakirim');            
            $table->foreignId('vendor_id')->constrained('vendors', 'vendor_id')->onDelete('cascade');                        
            $table->foreignId('employee_id')->nullable()->constrained('employees', 'employee_id')->onDelete('cascade');
            $table->string('code_jasakirim');
            $table->text('termin');
            $table->decimal('deposit', 12, 2);
            $table->decimal('sub_total', 12, 2);
            $table->decimal('ppn', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->boolean('approved')->default(false);
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
        Schema::dropIfExists('po_jasakirim');
    }
};
