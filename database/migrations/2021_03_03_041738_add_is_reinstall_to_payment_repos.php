<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsReinstallToPaymentRepos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_repos', function (Blueprint $table) {
            $table->boolean('is_reinstall')->default(false)->after('is_install');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_repos', function (Blueprint $table) {
            $table->dropColumn('is_reinstall');
        });
    }
}
