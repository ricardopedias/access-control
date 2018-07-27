<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Console\Commands;

use Illuminate\Console\Command;

class UserPanelOff extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     *
     * @var string
     */
    protected $signature = 'acl:user-panel-off {user : id ou email do usuário}';

    /**
     * A descrição do comando no terminal.
     *
     * @var string
     */
    protected $description = 'Nega o acesso ao painel para o usuário usuário especificado.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = $this->argument('user');

        if (\is_numeric($user)) {
            $model = \Acl\Models\AclUser::find($user);
        } else {
            $model = \Acl\Models\AclUser::where('email', $user)->first();
        }

        $model_status = \Acl\Models\AclUserStatus::find($model->id);

        if ($model_status->access_panel == 'no') {
            $this->info("Usuário já é impedido de acessar ao painel administrativo ID $model->id : $model->name <$model->email>");
            return true;
        }

        $model_status->access_panel = 'no';
        $model_status->save();

        $this->info("Negado o acesso ao painel administrativo para o usuário ID $model->id : $model->name <$model->email>");
    }
}
