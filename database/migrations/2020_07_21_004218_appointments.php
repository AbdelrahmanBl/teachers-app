<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Appointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id');
            $table->integer('days_id');
            
            $table->string('time_from',10);
            $table->string('time_to',10);

            $table->enum('year',[1,2,3]);
            $table->enum('status',['ON','OFF'])->default('ON');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP Table `appointments`');
    }
}
