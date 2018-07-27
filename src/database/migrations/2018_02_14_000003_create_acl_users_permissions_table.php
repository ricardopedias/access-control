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

class CreateAclUsersPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_users_permissions', function (Blueprint $table) {

            $table->unsignedInteger('role_id')->index();
            $table->foreign('role_id')->references('id')->on('acl_roles')->onDelete('cascade');

            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->enum('create', ['yes', 'no']);
            $table->enum('read', ['yes', 'no']);
            $table->enum('update', ['yes', 'no']);
            $table->enum('delete', ['yes', 'no']);

            $table->primary(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acl_users_permissions', function (Blueprint $table) {

            $slug = implode('_', ['acl_users_permissions', 'role_id', 'foreign']);
            $table->dropForeign($slug);

            $slug = implode('_', ['acl_users_permissions', 'user_id', 'foreign']);
            $table->dropForeign($slug);
        });

        Schema::dropIfExists('acl_users_permissions');
    }
}
