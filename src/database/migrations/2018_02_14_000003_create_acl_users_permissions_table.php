<?php

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
            $table->unsignedInteger('user_id')->index();

            $table->enum('create', ['yes', 'no']);
            $table->enum('edit', ['yes', 'no']);
            $table->enum('show', ['yes', 'no']);
            $table->enum('delete', ['yes', 'no']);

            $table->primary(['role_id', 'user_id']);

            $table->foreign('role_id')->references('id')->on('acl_roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
