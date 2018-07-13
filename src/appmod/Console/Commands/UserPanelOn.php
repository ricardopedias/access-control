<?php
namespace Acl\Console\Commands;

use Illuminate\Console\Command;

class UserPanelOn extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     *
     * @var string
     */
    protected $signature = 'acl:user-panel-on {user : id ou email do usuário}';

    /**
     * A descrição do comando no terminal.
     *
     * @var string
     */
    protected $description = 'Permite acesso ao painel para o usuário usuário especificado.';

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

        if ($model_status->access_panel == 'yes') {
            $this->info("Usuário já possui acesso ao painel administrativo ID $model->id : $model->name <$model->email>");
            return true;
        }

        $model_status->access_panel = 'yes';
        $model_status->save();

        $this->info("Liberado acesso ao painel administrativo para o usuário ID $model->id : $model->name <$model->email>");
    }
}
