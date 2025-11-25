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
        Schema::create('detailpackage', function (Blueprint $table) {
            $table->id('id_detail_package');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('products')->constrained('products', 'product_id')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->boolean('use_for_stock')->default(false);
            $table->boolean('has_used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailpackage');
    }
};
