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
            $table->foreignId('vendor_id')->constrained('vendors', 'vendor_id')->onDelete('cascade');                        
            $table->foreignId('id_employee')->constrained('employees', 'id_employee')->onDelete('cascade');
            $table->text('code_po');
            $table->text('termin');
            $table->integer('total_tax');            
            $table->string('status_payment');            
            $table->integer('sub_total');                        
            $table->integer('deposit');
            $table->integer('ppn');
            $table->integer('grand_total');
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
