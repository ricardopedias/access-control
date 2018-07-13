<?php
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

        $model->password == bcrypt($password);
        $model->save();

        $this->info("A senha do usuário foi atualizada ID $model->id : $model->name <$model->email>");
    }
}
