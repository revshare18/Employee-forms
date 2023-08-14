<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->nullable();
            $table->integer('leave_type_id')->nullable();
            $table->date('req_date_from')->nullable();
            $table->date('req_date_to')->nullable();
            $table->string('days_applied',20)->nullable();
            $table->string('reason',80)->nullable();
            $table->string('status',80);
            $table->date('date_submitted');
            $table->date('date_marked_manager')->nullable();
            $table->date('date_marked_admin')->nullable();
            $table->string('remarks',255)->nullable();
            $table->string('attachment',255)->nullable();
            $table->date('date_applied')->nullable();
            $table->string('time_in',20)->nullable();
            $table->string('time_out',20)->nullable();
            $table->string('OT_in',20)->nullable();
            $table->string('OT_out',20)->nullable();
            $table->string('total_hours',5)->nullable();
            $table->text('task')->nullable();
            $table->tinyInteger('trans_type')->comment('0=leave, 1 = overtime, 2 = in-out');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_requests');
    }
};
