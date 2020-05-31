<?php

namespace AmphiBee\Eloquent\Traits;

/**
 * Trait HasWOrdPressDbConn
 *
 * @package AmphiBee\Eloquent\Traits
 */
trait HasWordPressDbConn
{

    /** @var string */
    protected static $default_connection;

    /**
     * Allows setting a default connection on the model.
     *
     * @param string $connection
     * @return void
     */
    public static function setDefaultConnection(string $connection)
    {
        self::$default_connection = $connection;
    }

    /**
     * Initializes this trait and tracks which class just inited.
     *
     * @return void
     */
    protected function initializeHasWordPressDbConn()
    {
        if (isset(self::$default_connection)) {
            $this->connection = self::$default_connection;
        }
    }
}
