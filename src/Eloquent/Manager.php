<?php

namespace AmphiBee\Eloquent;

use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;

/**
 * Connection Resolver
 *
 * @package AmphiBee\Eloquent
 * @author AmphiBee <hello@amphibee.fr>
 */
class Manager extends CapsuleManager
{
    /**
     * Build the database manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $factory = new ConnectionFactory($this->container);

        $this->manager = new DatabaseManager($this->container, $factory);

        static::$instance = Connection::instance();
    }

    /**
     * Get a connection instance from the global manager.
     *
     * @param string|null $connection
     * @return \Illuminate\Database\Connection
     */
    public static function connection($connection = null)
    {
        return Connection::instance();
    }

    /**
     * Get a registered connection instance.
     *
     * @param string|null $name
     * @return \Illuminate\Database\Connection
     */
    public function getConnection($name = null)
    {
        return $this->manager->connection($name);
    }

    /**
     * Get a fluent query builder instance.
     *
     * @param \Closure|\Illuminate\Database\Query\Builder|string $table
     * @param string|null $as
     * @param string|null $connection
     * @return \Illuminate\Database\Query\Builder
     */
    public static function table($table, $as = null, $connection = null)
    {
        return self::connection($connection)->table($table, $as);
    }

    /**
     * Get a schema builder instance.
     *
     * @param string|null $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function schema($connection = null)
    {
        return self::connection($connection)->getSchemaBuilder();
    }
}
