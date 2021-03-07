<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Packages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image',64)->nullable();
            $table->string('name',20);
            $table->string('desc',100)->nullable();
            
            $table->integer('students_limit');
            $table->integer('appointment_limit');
            $table->integer('exams_limit');
            $table->double('price',15,3);
            
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
        DB::unprepared('DROP Table `packages`');
    }
}
