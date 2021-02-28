<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Exams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id');

            $table->string('exam_name',100);
            $table->string('desc',100)->nullable();
            
            $table->double('degree',5,1)->default(0);
            $table->integer('question_no')->default(0);
            $table->integer('duration');
            
            $table->boolean('is_published')->default(false);
            $table->boolean('is_hide')->default(false);
            $table->boolean('is_rtl');
            $table->enum('year',[1,2,3]);
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
        Schema::dropIfExists('exams');
    }
}
