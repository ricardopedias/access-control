<?php
namespace Acl\Console\Commands;

use Illuminate\Console\Command;

class Setup extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     *
     * @var string
     */
    protected $signature = 'acl:setup {task?}';

    /**
     * A descrição do comando no terminal.
     *
     * @var string
     */
    protected $description = 'Configura/atualiza configurações do Access Control';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task = $this->argument('task') ?? 'all';
        \Acl\Core::setup($task);
    }
}
