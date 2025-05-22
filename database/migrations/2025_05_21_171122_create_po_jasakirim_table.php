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
            $table->integer('termin');
            $table->integer('deposit');
            $table->integer('sub_total');
            $table->integer('ppn');
            $table->integer('grand_total');
            $table->boolean('approved')->nullable()->default(0);
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
