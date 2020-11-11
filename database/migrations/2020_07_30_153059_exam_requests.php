<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExamRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id');
            $table->integer('student_id');
            $table->integer('teacher_id');

            $table->boolean('is_corrected')->default(0);
            $table->boolean('is_seen')->default(0);
            $table->enum('status',['WAITING','IN_EXAM','NOT_EXAM','DICONNECTED','COMPLETED'])->default('WAITING');

            $table->string('duration_solve',10)->nullable();
            $table->double('total_degree',5,1)->nullable();
            $table->timestamp('start_at')->nullable(); 
            $table->timestamp('end_at')->nullable();    
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
        DB::unprepared('DROP Table `exam_requests`');
    }
}
