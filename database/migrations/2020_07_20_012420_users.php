<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->nullable();
            // $table->integer('appointment_id')->nullable();

            $table->string('first_name',15);
            $table->string('last_name',40);
            $table->string('email',64)->unique();
            $table->string('password',100);
            $table->string('mobile',11)->nullable();
            $table->string('parent_mobile1',11)->nullable();
            $table->string('parent_mobile2',11)->nullable();
            $table->string('image',64)->nullable()->comment('Image path');
            
            $table->enum('type',['S','T','A'])->comment('S : student , T : teacher , A : admin');
            $table->enum('year',[1,2,3])->nullable();
            $table->enum('status',['ON','OFF'])->default('ON');
            $table->enum('student_status',['WAITING','IN_EXAM'])->nullable();
            $table->integer('failed_try')->default(0);
            $table->integer('students_number')->nullable();
            $table->integer('appointments_number')->nullable();
            $table->integer('exams_number')->nullable();
            $table->boolean('accept_register')->nullable();
            $table->boolean('is_rtl')->nullable();
            $table->timestamp('last_login')->useCurrent();
            $table->rememberToken();
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
        DB::unprepared('DROP Table `users`');
    }
}
