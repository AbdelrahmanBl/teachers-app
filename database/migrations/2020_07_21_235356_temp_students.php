<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TempStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id');
            $table->integer('appointment_id');
            $table->integer('student_id')->nullable();
            $table->enum('year',[1,2,3])->nullable();
            $table->string('first_name',15);
            $table->string('last_name',40);
            $table->string('email',64)->unique()->nullable();
            $table->string('password',100)->nullable();
            $table->string('mobile',11);
            $table->string('parent_mobile1',11)->nullable();
            $table->string('parent_mobile2',11)->nullable();
            $table->enum('status',['ON','OFF'])->default('ON');
            $table->enum('type',['S','T','A'])->default('S');
            $table->enum('process',['register','subscrption'])->default('register');
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
        DB::unprepared('DROP Table `temp_students`');
    }
}
