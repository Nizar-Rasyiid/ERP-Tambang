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
            $table->foreignId('id_so')
                ->constrained('salesorders', 'id_so')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products', 'product_id')
                ->onDelete('cascade');
            $table->foreignId('package_id')
                ->nullable()
                ->constrained('package', 'package_id')
                ->onDelete('cascade');
            $table->enum('product_type', [
                'product',
                'package'
            ])->default('product');
            $table->integer('quantity');
            $table->integer('quantity_left');            
            $table->decimal('discount', 12, 2);
            $table->decimal('price', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->boolean('has_do')->default(false);
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
