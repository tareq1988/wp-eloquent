<?php

namespace WeDevs\ORM\Eloquent;

use Illuminate\Database\Query\Builder as EloquentBuilder;
use Illuminate\Database\Query\JoinClause;

/**
 * QueryBuilder Class
 *
 * @package WeDevs\ERP\Framework
 */
class Builder extends EloquentBuilder {

	/**
	 * The database connection instance.
	 *
	 * @var \WeDevs\ORM\Eloquent\Database;
	 */
	public $connection;

	/**
	 * Add a join clause to the query.
	 *
	 * @param string      $table
	 * @param string      $first
	 * @param string|null $operator
	 * @param string|null $second
	 * @param string      $type
	 * @param bool        $where
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
	{
		return parent::join($this->getConnection()->getTableName($table), $first, $operator, $second, $type, $where);
	}

	/**
	 * Add a "cross join" clause to the query.
	 *
	 * @param string      $table
	 * @param string|null $first
	 * @param string|null $operator
	 * @param string|null $second
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function crossJoin($table, $first = null, $operator = null, $second = null)
	{
		if ($first) {
			return $this->join($table, $first, $operator, $second, 'cross');
		}

		$this->joins[] = new JoinClause($this, 'cross', $this->getConnection()->getTableName($table));

		return $this;
	}

	/**
     * Add an exists clause to the query.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function addWhereExistsQuery(EloquentBuilder $query, $boolean = 'and', $not = false) {
        
        $type = $not ? 'NotExists' : 'Exists';

        $this->wheres[] = compact('type', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

	/**
	 * Get the database connection instance.
	 *
	 * @return \WeDevs\ORM\Eloquent\Database
	 */
	public function getConnection()
	{
		return $this->connection;
	}
}
