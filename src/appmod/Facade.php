<?php

namespace Laracl;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return Accessor::class;
    }
}
