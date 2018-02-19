<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersAclGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Roles: visitantes, administradores, gerentes, etc
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedInteger('acl_group_id')->default(1);
            $table->foreign('acl_group_id')->references('id')->on('acl_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $slug = implode('_', ['users', 'acl_group_id', 'foreign']);
            $table->dropForeign($slug);

            $table->dropColumn('acl_group_id');
        });
    }
}
