<?php

namespace Laracl\Tests\Libs;

use Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Laracl\Models;
use Illuminate\Support\Str;
use Laracl\Tests\Libs\IModelTestCase;

class IControllerTestCase extends IModelTestCase
{
    public function setUp()
    {
        // Cria a aplicação e inicia o laravel
        parent::setUp();

        $path = explode('\\', get_called_class());
        $class = array_pop($path);
        $data_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $class . ".sqlite";
        exec("touch " . $data_file);

        $this->app['config']->set('database.connections.sqlite.driver', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', $data_file);

        $this->app['config']->set('database.default','sqlite');

        \Artisan::call('migrate');
        \Artisan::call('migrate', ['--path' => 'vendor/plexi/laracl/src/database/migrations']);

        self::createUser();
    }
}
