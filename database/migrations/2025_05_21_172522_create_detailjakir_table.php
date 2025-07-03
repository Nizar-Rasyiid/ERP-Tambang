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
        Schema::create('detailjakir', function (Blueprint $table) {
            $table->id('id_detailjakir');
            $table->foreignId('id_jasakirim')->constrained('po_jasakirim', 'id_jasakirim')->onDelete('cascade');
            $table->text('product_name');
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailjakir');
    }
};
