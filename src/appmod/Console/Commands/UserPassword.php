<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Console\Commands;

use Illuminate\Console\Command;

class UserPassword extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     *
     * @var string
     */
    protected $signature = 'acl:user-pass {user : id ou email do usuário} {password}';

    /**
     * A descrição do comando no terminal.
     *
     * @var string
     */
    protected $description = 'MUda a senha do usuário especificado.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = $this->argument('user');
        $password = $this->argument('password');

        if (\is_numeric($user)) {
            $model = \Acl\Models\AclUser::find($user);
        } else {
            $model = \Acl\Models\AclUser::where('email', $user)->first();
        }

        $model->password = bcrypt($password);
        $result = $model->save();
        if ($result === true) {
            $this->info("A senha do usuário foi atualizada ID $model->id : $model->name <$model->email>");
        } else {
            $this->error("A senha do não pôde ser alterada ID $model->id : $model->name <$model->email>");
        }
    }
}
