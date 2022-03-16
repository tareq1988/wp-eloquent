<?php

namespace AmphiBee\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

/**
 * Model Class
 *
 * @package DG\ORM
 */
abstract class Model extends Eloquent
{
    /**
     * Model constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        static::$resolver = new Resolver();
        parent::__construct($attributes);
    }

    /**
     * Get the database connection for the model.
     *
     * @return Database
     */
    public function getConnection()
    {
        return Connection::instance();
    }

    /**
     * Get the table associated with the model.
     *
     * Append the WordPress table prefix with the table name if
     * no table name is provided
     *
     * @return string
     */
    public function getTable()
    {
        $table = $this->table ?: str_replace('\\', '', Str::snake(Str::plural(class_basename($this))));

        return $this->getConnection()->prefixTable($table);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param string $childType
     * @param mixed $value
     * @param string|null $field
     *
     * @return Eloquent|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        $relationship = $this->{Str::plural(Str::camel($childType))}();

        if ($relationship instanceof HasManyThrough || $relationship instanceof BelongsToMany) {
            $table = $this->getConnection()->prefixTable(
                $relationship->getRelated()->getTable()
            );

            return $relationship->where($table . '.' . $field, $value)->first();
        } else {
            return $relationship->where($field, $value)->first();
        }
    }
}
