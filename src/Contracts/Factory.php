<?php

namespace Teamupdivision\SaltId\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param  string  $driver
     * @return \Teamupdivision\SaltId\Contracts\Provider
     */
    public function driver($driver = null);
}
