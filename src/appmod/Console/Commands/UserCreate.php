<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Console\Commands;

use Illuminate\Console\Command;

class UserCreate extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     *
     * @var string
     */
    protected $signature = 'acl:user-create {name} {email} {password}';

    /**
     * A descrição do comando no terminal.
     *
     * @var string
     */
    protected $description = 'Cria um novo usuário no sistema';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $model = \Acl\Models\AclUser::create([
            'name'     => $name,
            'email'    => $email,
            'password' => bcrypt($password)
        ]);

        \Acl\Models\AclUserStatus::create([
            'user_id'      => $model->id,
            'access_panel' => 'yes',
            'status'       => 'active'
        ]);

        $this->info("Usuário criado com sucesso ID $model->id : $name <$email>");
    }
}
