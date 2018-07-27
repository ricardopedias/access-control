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

class CreateAclUsersGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Roles: visitantes, administradores, gerentes, etc
        Schema::create('acl_users_groups', function (Blueprint $table) {

            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('group_id')->index();
            $table->foreign('group_id')->references('id')->on('acl_groups')->onDelete('cascade');

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
        Schema::table('acl_users_groups', function (Blueprint $table) {

            $slug = implode('_', ['acl_users_groups', 'user_id', 'foreign']);
            $table->dropForeign($slug);

            $slug = implode('_', ['acl_users_groups', 'group_id', 'foreign']);
            $table->dropForeign($slug);
        });

        Schema::dropIfExists('acl_users_groups');
    }
}
