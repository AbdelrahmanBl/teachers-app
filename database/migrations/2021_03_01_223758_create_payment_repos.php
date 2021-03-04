<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentRepos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_repos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('desc')->nullable();

            $table->integer('teacher_id');
            $table->integer('year');
            $table->string('appointment_ids');

            $table->integer('students_no')->default(0);

            $table->boolean('is_paid')->default(0);
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
        Schema::dropIfExists('payment_repos');
    }
}
