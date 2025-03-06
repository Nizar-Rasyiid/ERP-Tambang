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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->text('customer_code');
            $table->text('customer_name');
            $table->text('customer_toko');
            $table->text('customer_singkatan');
            $table->integer('customer_phone');
            $table->string('customer_email')->unique();
            $table->text('customer_address');
            $table->integer('customer_npwp')->length(16);
            $table->string('customer_contact');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
