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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
                $table->integer('employee_code');
                $table->text('employee_name');
                $table->text('employee_phone');
                $table->string('employee_email')->unique();
                $table->text('employee_address');
                $table->integer('employee_salary');
                $table->date('employee_end_contract');
                $table->integer('employee_nik')->length(16);
                $table->text('employee_position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
