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
            $table->foreignId('id_so')->constrained('salesorders', 'id_so')->onDelete('cascade');
            $table->integer('price');
            $table->string('payment_method')->nullable();
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
