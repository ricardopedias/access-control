<?php

namespace Laracl\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Laracl\Tests\Libs\IModelTestCase;

class AcessorTest extends IModelTestCase
{
    use RefreshDatabase;

    public function testeNone()
    {
        $this->assertTrue(true);
    }
}
