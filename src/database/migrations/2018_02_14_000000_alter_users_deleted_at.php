<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Acl\Models\AclGroup;

class AlterUsersDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_at') == false) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        O campo não será removido!
        Motivo: caso o projeto anterior, sem o LarACL já utilize softdeletes,
        esta rotina removeria uma informação importante.
        O fato de existir um campo chamado 'deleted_at' não altera em nada o funcionmento
        de um projeto. Podendo ser removido posteriormente de forma manual.
        */

        /*
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        */
    }
}
