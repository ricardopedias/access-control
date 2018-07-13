<?php
namespace Acl\Console\Commands;

use Illuminate\Console\Command;

class UserOff extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     *
     * @var string
     */
    protected $signature = 'acl:user-off {user : id ou email do usuário}';

    /**
     * A descrição do comando no terminal.
     *
     * @var string
     */
    protected $description = 'Desativa o usuário especificado.';

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

        if ($model_status->status == 'inactive') {
            $this->info("Usuário já está inativo ID $model->id : $model->name <$model->email>");
            return true;
        }

        $model_status->status = 'inactive';
        $model_status->save();

        $this->info("Usuário inativado com sucesso ID $model->id : $model->name <$model->email>");
    }
}
