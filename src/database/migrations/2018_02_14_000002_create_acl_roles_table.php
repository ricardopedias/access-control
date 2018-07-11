<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Acl\Models\AclRole;

class CreateAclRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Roles: visitantes, administradores, gerentes, etc
        Schema::create('acl_roles', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        AclRole::create([
            'name' => 'Users',
            'slug' => 'users',
            'description' => 'Gerenciamento de usuários',
            ]);

        AclRole::create([
            'name' => 'Users Permissions',
            'slug' => 'users-permissions',
            'description' => 'Permissões de Usuários',
            ]);

        AclRole::create([
            'name' => 'Groups',
            'slug' => 'groups',
            'description' => 'Grupos de Acesso',
            ]);

        AclRole::create([
            'name' => 'Groups Permissions',
            'slug' => 'groups-permissions',
            'description' => 'Permissões de Grupos',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_roles');
    }
}
