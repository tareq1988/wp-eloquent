<?php
namespace WeDevs\ORM\Eloquent;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Expression;

class Database implements ConnectionInterface {

    public $db;

    /**
     * Initializes the Database class
     *
     * @return \WeDevs\ORM\Eloquent\Database
     */
    public static function instance() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * [__construct description]
     */
    public function __construct() {
        global $wpdb;

        $this->db = $wpdb;
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param  string $table
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function table( $table ) {
        $processor = $this->getPostProcessor();

        $table = $this->db->prefix . $table;

        $query = new Builder( $this, $this->getQueryGrammar(), $processor );

        return $query->from( $table );
    }

    /**
     * Get a new raw query expression.
     *
     * @param  mixed $value
     *
     * @return \Illuminate\Database\Query\Expression
     */
    public function raw( $value ) {
        return new Expression( $value );
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return mixed
     */
    public function selectOne( $query, $bindings = array() ) {
        $query = $this->bind_params( $query, $bindings );

        return $this->db->get_row( $query );
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return array
     */
    public function select( $query, $bindings = array() ) {
        $query = $this->bind_params( $query, $bindings );

        return $this->db->get_results( $query );
    }

    /**
     * A hacky way to emulate bind parameters into SQL query
     *
     * @param $query
     * @param $bindings
     *
     * @return mixed
     */
    private function bind_params( $query, $bindings, $update = false ) {

        $query    = str_replace( '"', '`', $query );
        $bindings = $this->prepareBindings( $bindings );

        if ( ! $bindings ) {
            return $query;
        }

        $bindings = array_map( function( $replace ) {
            if ( is_string( $replace ) ) {
                $replace = "'" . esc_sql( $replace ) . "'";
            } elseif ( $replace === null ) {
                $replace = "null";
            }

            return $replace;
        }, $bindings );

        $query = str_replace( array( '%', '?' ), array( '%%', '%s' ), $query );
        $query = vsprintf( $query, $bindings );

        return $query;
    }

    /**
     * Bind and run the query
     *
     * @param  string  $query
     * @param  array  $bindings
     *
     * @return array
     */
    public function bind_and_run( $query, $bindings = array() ) {
        $new_query = $this->bind_params( $query, $bindings );

        $this->db->query( $new_query );
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return bool
     */
    public function insert( $query, $bindings = array() ) {
        $this->bind_and_run( $query, $bindings );
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return int
     */
    public function update( $query, $bindings = array() ) {
        $new_query = $this->bind_params( $query, $bindings, true );

        $this->db->query( $new_query );
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return int
     */
    public function delete( $query, $bindings = array() ) {
        $this->bind_and_run( $query, $bindings );
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return bool
     */
    public function statement( $query, $bindings = array() ) {
        // TODO: Implement statement() method.
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string $query
     * @param  array  $bindings
     *
     * @return int
     */
    public function affectingStatement( $query, $bindings = array() ) {
        // TODO: Implement affectingStatement() method.
    }

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param  string $query
     *
     * @return bool
     */
    public function unprepared( $query ) {
        // TODO: Implement unprepared() method.
    }

    /**
     * Prepare the query bindings for execution.
     *
     * @param  array $bindings
     *
     * @return array
     */
    public function prepareBindings( array $bindings ) {
        $grammar = $this->getQueryGrammar();

        foreach ( $bindings as $key => $value ) {

            // We need to transform all instances of the DateTime class into an actual
            // date string. Each query grammar maintains its own date string format
            // so we'll just ask the grammar for the format to get from the date.
            if ( $value instanceof DateTime ) {
                $bindings[ $key ] = $value->format( $grammar->getDateFormat() );
            } elseif ( $value === false ) {
                $bindings[ $key ] = 0;
            }
        }

        return $bindings;
    }

    /**
     * Execute a Closure within a transaction.
     *
     * @param  \Closure $callback
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function transaction( \Closure $callback ) {
        // TODO: Implement transaction() method.
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction() {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit() {
        // TODO: Implement commit() method.
    }

    /**
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack() {
        // TODO: Implement rollBack() method.
    }

    /**
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel() {
        // TODO: Implement transactionLevel() method.
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param  \Closure $callback
     *
     * @return array
     */
    public function pretend( \Closure $callback ) {
        // TODO: Implement pretend() method.
    }

    public function getPostProcessor() {
        return new Processor();
    }

    public function getQueryGrammar() {
        return new Grammar();
    }

    /**
     * Return self as PDO
     *
     * @return \WeDevs\ORM\Eloquent\Database
     */
    public function getPdo() {
        return $this;
    }

    /**
     * Return the last insert id
     *
     * @param  string  $args
     *
     * @return int
     */
    public function lastInsertId( $args ) {
        return $this->db->insert_id;
    }
}
