<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Subscrptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscrptions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('teacher_id');
            $table->integer('student_id');
            $table->integer('appointment_id');
            $table->integer('temp_id')->nullable();

            $table->integer('attend_no')->default(0);
            $table->integer('missed_no')->default(0);
            $table->integer('missed_sequence')->default(0);
            $table->double('student_rate',5,1)->nullable();

            // $table->enum('type',['register','subscrption']);
            $table->enum('status',['ON','OFF'])->default('ON');
            
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
        Schema::dropIfExists('subscrptions');
    }
}
