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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id('vendor_id');            
            $table->string('vendor_name');            
            $table->string('vendor_email');
            $table->string('vendor_phone');
            $table->string('vendor_address')->nullable();
            $table->string('vendor_singkatan');     
            $table->string('vendor_npwp')->nullable();
            $table->string('vendor_contact');         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
