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
        Schema::create('tandaterima', function (Blueprint $table) {
            $table->id('id_tandater');            
            $table->foreignId('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('cascade');
                
            $table->foreignId('id_so')
                ->references('id_so')
                ->on('salesorders')
                ->onDelete('cascade');

            $table->text('code_tandater');
            $table->text('resi');
            $table->date('issue_at');
            $table->date('due_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tandaterima');
    }
};
