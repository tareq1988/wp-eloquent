<?php
namespace AmphiBee\Eloquent;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database extends \Corcel\Database
{
    /**
     * @param array $params
     * @return \Illuminate\Database\Capsule\Manager
     */
    public static function connect(array $params = [])
    {
        if ( count( $params ) === 0 ) {
            $params = [
                'driver'    => 'mysql',
                'host'      => 'database',
                'database'  => DB_NAME,
                'username'  => DB_USER,
                'password'  => DB_PASSWORD,
                'prefix'    => env('DB_PREFIX') ?: 'wp_' // default prefix is 'wp_', you can change to your own prefix
            ];
        }

        $capsule = new Capsule();

        $params = array_merge(static::$baseParams, $params);
        $capsule->addConnection($params);
        $capsule->bootEloquent();
        return $capsule;
    }
}
