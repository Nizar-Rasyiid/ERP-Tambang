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
        Schema::create('detailso', function (Blueprint $table) {
            $table->id('id_detail_so');
            $table->foreignId('id_so')->constrained('salesorders', 'id_so')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade')->nullable();
            $table->foreignId('package_id')->constrained('package', 'package_id')->onDelete('cascade')->nullable();
            $table->string('product_type');
            $table->integer('quantity');
            $table->tinyInteger('has_do');
            $table->integer('quantity_left');
            $table->integer('discount');
            $table->integer('price');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailso');
    }
};
