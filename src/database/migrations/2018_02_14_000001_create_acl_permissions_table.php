<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_permissions', function (Blueprint $table) {

            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('role_id')->index();

            $table->enum('create', ['yes', 'no']);
            $table->enum('edit', ['yes', 'no']);
            $table->enum('show', ['yes', 'no']);
            $table->enum('delete', ['yes', 'no']);

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
        Schema::table('acl_permissions', function (Blueprint $table) {

            $slug = implode('_', ['acl_permissions', 'role_id', 'foreign']);
            $table->dropForeign($slug);
        });

        Schema::dropIfExists('acl_permissions');
    }
}
