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
        Schema::create('assets', function (Blueprint $table) {
            $table->id('asset_id');
            $table->foreignId('vendor_id')->constrained('vendors', 'vendor_id')->onDelete('cascade');                        
            $table->text('code');
            $table->text('assets_name');  
            $table->integer('price');               
            $table->integer('assets_life');      
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
        Schema::dropIfExists('assets');
    }
};
