<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceReposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_repos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('teacher_id');
            $table->integer('class_no');
            $table->integer('month');
            $table->integer('year');
            $table->integer('appointment_id');
        
            $table->boolean('is_install')->default(0);

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
        Schema::dropIfExists('attendance_repos');
    }
}
