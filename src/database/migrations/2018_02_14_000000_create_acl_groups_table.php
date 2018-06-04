<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Laracl\Models\AclGroup;

class CreateAclGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Roles: visitantes, administradores, gerentes, etc
        Schema::create('acl_groups', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();
            $table->enum('system', ['yes', 'no'])->default('no');
            $table->timestamps();

            $table->softDeletes();
        });

        AclGroup::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administradores',
            'system' => 'yes',
            ]);

        AclGroup::create([
            'name' => 'Users',
            'slug' => 'users',
            'description' => 'Usuários comuns',
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
        Schema::dropIfExists('acl_groups');
    }
}
