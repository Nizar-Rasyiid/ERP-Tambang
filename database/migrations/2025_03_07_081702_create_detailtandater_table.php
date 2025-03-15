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
        Schema::create('detailtandater', function (Blueprint $table) {
            $table->id('id_detail_tandater');
            $table->foreignId('id_tandater')->constrained('tandaterima', 'id_tandater')->onDelete('cascade');            
            $table->foreignId('id_invoice')->constrained('invoices', 'id_invoice')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailtandater');
    }
};
