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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('dept_group_id')->nullable();
            $table->integer('designation_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('emp_id',20)->nullable();
            $table->string('middlename',80)->nullable();
            $table->string('lastname',80)->nullable();
            $table->date('dob')->nullable();
            $table->string('contact',30)->nullable();
            $table->string('tin',50)->nullable();
            $table->string('address',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
