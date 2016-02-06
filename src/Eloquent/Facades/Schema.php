<?php

namespace WeDevs\ORM\Eloquent\Facades;

use Illuminate\Support\Facades\Facade;
use WeDevs\ORM\Eloquent\Database;

/**
 * @see \Illuminate\Database\Schema\Builder
 */
class Schema extends Facade
{
    /**
     * Get a schema builder instance for the default connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected static function getFacadeAccessor()
    {
        return Database::instance()->getSchemaBuilder();
    }
}
