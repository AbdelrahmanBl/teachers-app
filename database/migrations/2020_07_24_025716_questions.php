<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Questions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('questions', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('exam_id');
        //     $table->string('image',64)->nullable();

        //     $table->enum('question_type',['M','W'])->comment('M : MCQ , W : Writing');
        //     $table->text('main_question')->nullable();
        //     $table->string('question')->nullable();
        //     $table->string('true_respond')->nullable();
        //     $table->text('responds')->nullable();
        //     $table->string('outside_counter')->nullable();
        //     $table->string('inside_counter')->nullable();
            
        //     $table->double('degree',5,1);
                        
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP Table `questions`');
    }
}
