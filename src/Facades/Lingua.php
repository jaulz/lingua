<?php

namespace Jaulz\Lingua\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jaulz\Lingua\Lingua
 */
class Lingua extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Jaulz\Lingua\Lingua::class;
    }
}