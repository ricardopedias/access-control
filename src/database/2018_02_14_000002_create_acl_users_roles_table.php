<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclUsersRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_users_roles', function (Blueprint $table) {

            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('role_id')->index();

            $table->primary(['user_id', 'role_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('acl_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acl_users_roles', function (Blueprint $table) {

            $slug = implode('_', ['acl_users_roles', 'user_id', 'foreign']);
            $table->dropForeign($slug);

            $slug = implode('_', ['acl_users_roles', 'role_id', 'foreign']);
            $table->dropForeign($slug);
        });

        Schema::dropIfExists('acl_users_roles');
    }
}
