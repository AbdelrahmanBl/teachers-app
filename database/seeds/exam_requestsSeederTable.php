<?php

use Illuminate\Database\Seeder;
use App\Models\ExamRequest;

class exam_requestsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ExamRequest::truncate();
        factory( ExamRequest::class , 1000 )->create();
    }
}
