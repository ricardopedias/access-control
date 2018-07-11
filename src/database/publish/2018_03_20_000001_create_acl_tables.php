<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createAclTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('migrate', ['--path' => 'vendor/plexi/access-control/src/database/migrations']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Artisan::call('migrate:reset', ['--path' => 'vendor/plexi/access-control/src/database/migrations']);
    }
}
