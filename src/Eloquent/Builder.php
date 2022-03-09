<?php

namespace AmphiBee\Eloquent;

use Illuminate\Database\Query\Builder as EloquentBuilder;

/**
 * QueryBuilder Class
 *
 * @package DG\ERP\Framework
 */
class Builder extends EloquentBuilder
{
    /**
     * Add an exists clause to the query.
     *
     * @param EloquentBuilder $query
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function addWhereExistsQuery(EloquentBuilder $query, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $this->wheres[] = compact('type', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

}
