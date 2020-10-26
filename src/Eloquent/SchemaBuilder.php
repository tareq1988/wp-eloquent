<?php

namespace AmphiBee\Eloquent;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;

/**
 * QueryBuilder Class
 *
 * @package DG\ERP\Framework
 */
class SchemaBuilder extends Builder
{

    /**
     * Execute the blueprint to build / modify the table.
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @return void
     */
    public function build(Connection $connection)
    {
        foreach ($this->toSql($connection) as $statement) {
            $connection->statement($statement);
        }
    }
}
