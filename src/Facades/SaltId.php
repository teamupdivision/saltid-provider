<?php

namespace Teamupdivision\SaltId\Facades;

use Illuminate\Support\Facades\Facade;
use Teamupdivision\SaltId\Contracts\Factory;

/**
 * @method static \Teamupdivision\SaltId\Contracts\Provider driver(string $driver = null)
 * @method static \Teamupdivision\SaltId\Two\AbstractProvider buildProvider($provider, $config)
 *
 * @see \Teamupdivision\SaltId\SaltIdManager
 */
class SaltId extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
