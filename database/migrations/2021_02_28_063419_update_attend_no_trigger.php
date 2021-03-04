<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAttendNoTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER UpdateAttendNoTrigger BEFORE UPDATE ON `subscrptions` FOR EACH ROW
            BEGIN
                IF NEW.attend_no <> OLD.attend_no THEN
                    SET NEW.missed_no       = OLD.missed_no        + (OLD.attend_no - NEW.attend_no);
                    SET NEW.missed_sequence = OLD.missed_sequence  + (OLD.attend_no - NEW.attend_no);
                END IF;

                IF NEW.missed_sequence < 0 THEN
                    SET NEW.missed_sequence = 0;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `UpdateAttendNoTrigger`');
    }
}
