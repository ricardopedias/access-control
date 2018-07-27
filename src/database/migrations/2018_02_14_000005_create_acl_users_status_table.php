<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclUsersStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_users_status', function (Blueprint $table) {

            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->enum('access_panel',['yes', 'no'])->default('no');
            $table->enum('status',['active', 'inactive'])->default('active');

            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acl_users_status', function (Blueprint $table) {

            $slug = implode('_', ['acl_users_status', 'user_id', 'foreign']);
            $table->dropForeign($slug);

        });

        Schema::dropIfExists('acl_users_status');
    }
}
