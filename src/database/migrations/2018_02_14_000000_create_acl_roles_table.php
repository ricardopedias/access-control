<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Laracl\Models\AclRole;

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
            $table->enum('system', ['yes', 'no'])->default('no');
            $table->timestamps();
        });

        AclRole::create([
            'name' => 'Users',
            'slug' => 'users',
            'description' => 'Gerenciamento de usuários',
            'system' => 'yes',
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
