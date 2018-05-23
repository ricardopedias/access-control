<?php

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

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('group_id')->index();

            $table->primary(['user_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('acl_groups')->onDelete('cascade');
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
