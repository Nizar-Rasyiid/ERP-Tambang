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
        Schema::create('opex', function (Blueprint $table) {
            $table->id('opex_id');
            $table->string('opex_code');
            $table->string('opex_name');
            $table->string('opex_type');
            $table->decimal('opex_price',12,2);
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id')->onDelete('cascade');            
            $table->boolean('approved')->default(false);
            $table->date('issue_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opex');
    }
};
