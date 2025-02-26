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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');            
            $table->integer('product_code');
            $table->string('product_sn');
            $table->string('product_desc');
            $table->string('product_brand');
            $table->string('product_uom');
            $table->integer('product_stock');
            $table->text('product_image')->nullable();                                    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
