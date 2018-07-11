<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclGroupsPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_groups_permissions', function (Blueprint $table) {

            $table->unsignedInteger('role_id')->index();
            $table->foreign('role_id')->references('id')->on('acl_roles')->onDelete('cascade');

            $table->unsignedInteger('group_id')->index();
            $table->foreign('group_id')->references('id')->on('acl_groups')->onDelete('cascade');

            $table->enum('create', ['yes', 'no']);
            $table->enum('read', ['yes', 'no']);
            $table->enum('update', ['yes', 'no']);
            $table->enum('delete', ['yes', 'no']);

            $table->primary(['role_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acl_groups_permissions', function (Blueprint $table) {

            $slug = implode('_', ['acl_groups_permissions', 'role_id', 'foreign']);
            $table->dropForeign($slug);

            $slug = implode('_', ['acl_groups_permissions', 'group_id', 'foreign']);
            $table->dropForeign($slug);
        });

        Schema::dropIfExists('acl_groups_permissions');
    }
}
