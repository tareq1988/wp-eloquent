<?php

namespace AmphiBee\Eloquent;

use AmphiBee\Eloquent\Manager as Capsule;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar as QueryGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Arr;

/**
 * Connection Resolver
 *
 * @package AmphiBee\Eloquent
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class Connection implements ConnectionInterface
{
    public $db;
    public $pdo;

    public $schemaGrammar;

    /**
     * The query post processor implementation.
     *
     * @var \Illuminate\Database\Query\Processors\Processor
     */
    protected $postProcessor;

    /**
     * The query grammar implementation.
     *
     * @var \Illuminate\Database\Query\Grammars\Grammar
     */
    protected $queryGrammar;

    /**
     * Count of active transactions
     *
     * @var int
     */
    public $transactionCount = 0;

    /**
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = [];

    /**
     * All of the queries run against the connection.
     *
     * @var array
     */
    protected $queryLog = [];

    /**
     * Indicates whether queries are being logged.
     *
     * @var bool
     */
    protected $loggingQueries = false;

    /**
     * Initializes the Database class
     *
     * @return Database
     */
    public static function instance()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * [__construct description]
     */
    public function __construct()
    {
        global $wpdb;

        $this->config = [
            'name' => 'wp-eloquent-mysql2',
        ];
        $this->db = $wpdb;
        $this->pdo = new WpdbPdo($wpdb);
    }

    /**
     * Get the database connection name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getConfig('name');
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param string $table
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table, $as = null)
    {
        $processor = $this->getPostProcessor();

        $table = $this->prefixTable($table);

        $query = new Builder($this, $this->getQueryGrammar(), $processor);

        return $query->from($table);
    }

    /**
     * Get a new raw query expression.
     *
     * @param mixed $value
     *
     * @return \Illuminate\Database\Query\Expression
     */
    public function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return new Builder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor()
        );
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return mixed
     * @throws QueryException
     *
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->bind_params($query, $bindings);

        $result = $this->db->get_row($query);

        if ($result === false || $this->db->last_error) {
            throw new QueryException($query, $bindings, new \Exception($this->db->last_error));
        }

        return $result;
    }

    /**
     * Run a select statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array
     * @throws QueryException
     *
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->bind_params($query, $bindings);

        $result = $this->db->get_results($query);

        if ($result === false || $this->db->last_error) {
            dd($query);
            throw new QueryException($query, $bindings, new \Exception($this->db->last_error));
        }

        return $result;
    }

    /**
     * Run a select statement against the database and returns a generator.
     * TODO: Implement cursor and all the related sub-methods.
     *
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return \Generator
     */
    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        $statement = $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            // First we will create a statement for the query. Then, we will set the fetch
            // mode and prepare the bindings for the query. Once that's done we will be
            // ready to execute the query against the database and return the cursor.
            $statement = $this->prepared($this->getPdoForSelect($useReadPdo)
                ->prepare($query));

            $this->bindValues(
                $statement,
                $this->prepareBindings($bindings)
            );

            // Next, we'll execute the query against the database and return the statement
            // so we can return the cursor. The cursor will use a PHP generator to give
            // back one row at a time without using a bunch of memory to render them.
            $statement->execute();

            return $statement;
        });

        while ($record = $statement->fetch()) {
            yield $record;
        }
    }

    /**
     * Get the PDO connection to use for a select query.
     *
     * @param bool $useReadPdo
     * @return \PDO
     */
    protected function getPdoForSelect($useReadPdo = true)
    {
        return $useReadPdo ? $this->getReadPdo() : $this->getPdo();
    }

    /**
     * Get the current PDO connection.
     *
     * @return \PDO
     */
    public function getPdo()
    {
        if ($this->pdo instanceof \Closure) {
            return $this->pdo = call_user_func($this->pdo);
        }

        return $this->pdo;
    }

    /**
     * Get the current PDO connection used for reading.
     *
     * @return \PDO
     */
    public function getReadPdo()
    {
        if ($this->transactions > 0) {
            return $this->getPdo();
        }

        if ($this->recordsModified && $this->getConfig('sticky')) {
            return $this->getPdo();
        }

        if ($this->readPdo instanceof \Closure) {
            return $this->readPdo = call_user_func($this->readPdo);
        }

        return $this->readPdo ?: $this->getPdo();
    }

    /**
     * Set the PDO connection.
     *
     * @param \PDO|\Closure|null $pdo
     * @return $this
     */
    public function setPdo($pdo)
    {
        $this->transactions = 0;

        $this->pdo = $pdo;

        return $this;
    }

    /**
     * Set the PDO connection used for reading.
     *
     * @param \PDO|\Closure|null $pdo
     * @return $this
     */
    public function setReadPdo($pdo)
    {
        $this->readPdo = $pdo;

        return $this;
    }


    /**
     * A hacky way to emulate bind parameters into SQL query
     *
     * @param $query
     * @param $bindings
     *
     * @param bool $update
     *
     * @return mixed
     */
    private function bind_params($query, $bindings, $update = false)
    {
        $query = str_replace('"', '`', $query);
        $bindings = $this->prepareBindings($bindings);

        if (!$bindings) {
            return $query;
        }

        $bindings = array_map(function ($replace) {
            if (is_string($replace)) {
                $replace = "'" . esc_sql($replace) . "'";
            } elseif ($replace === null) {
                $replace = "null";
            }

            return $replace;
        }, $bindings);

        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings);

        return $query;
    }

    /**
     * Bind and run the query
     *
     * @param string $query
     * @param array $bindings
     * @return array
     * @throws QueryException
     *
     */
    public function bind_and_run($query, $bindings = array())
    {
        $new_query = $this->bind_params($query, $bindings);

        $result = $this->db->query($new_query);

        if ($result === false || $this->db->last_error) {
            throw new QueryException($new_query, $bindings, new \Exception($this->db->last_error));
        }

        return (array)$result;
    }

    /**
     * Run an insert statement against the database.
     *
     * @param string $query
     * @param array $bindings
     *
     * @return bool
     */
    public function insert($query, $bindings = array())
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param string $query
     * @param array $bindings
     *
     * @return int
     */
    public function update($query, $bindings = array())
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param string $query
     * @param array $bindings
     *
     * @return int
     */
    public function delete($query, $bindings = array())
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param string $query
     * @param array $bindings
     *
     * @return bool
     */
    public function statement($query, $bindings = array())
    {
        $new_query = $this->bind_params($query, $bindings, true);

        return $this->unprepared($new_query);
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param string $query
     * @param array $bindings
     *
     * @return int
     */
    public function affectingStatement($query, $bindings = array())
    {
        $new_query = $this->bind_params($query, $bindings, true);

        $result = $this->db->query($new_query);

        if ($result === false || $this->db->last_error) {
            throw new QueryException($new_query, $bindings, new \Exception($this->db->last_error));
        }

        return intval($result);
    }

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param string $query
     *
     * @return bool
     */
    public function unprepared($query)
    {
        $result = $this->db->query($query);

        return ($result === false || $this->db->last_error);
    }

    /**
     * Prepare the query bindings for execution.
     *
     * @param array $bindings
     *
     * @return array
     */
    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value) {

            // Micro-optimization: check for scalar values before instances
            if (is_bool($value)) {
                $bindings[$key] = intval($value);
            } elseif (is_scalar($value)) {
                continue;
            } elseif ($value instanceof \DateTime) {
                // We need to transform all instances of the DateTime class into an actual
                // date string. Each query grammar maintains its own date string format
                // so we'll just ask the grammar for the format to get from the date.
                $bindings[$key] = $value->format($grammar->getDateFormat());
            }
        }

        return $bindings;
    }

    /**
     * Execute a Closure within a transaction.
     *
     * @param \Closure $callback
     * @param int $attempts
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function transaction(\Closure $callback, $attempts = 1)
    {
        $this->beginTransaction();
        try {
            $data = $callback();
            $this->commit();
            return $data;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $transaction = $this->unprepared("START TRANSACTION;");
        if (false !== $transaction) {
            $this->transactionCount++;
        }
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit()
    {
        if ($this->transactionCount < 1) {
            return;
        }
        $transaction = $this->unprepared("COMMIT;");
        if (false !== $transaction) {
            $this->transactionCount--;
        }
    }

    /**
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack()
    {
        if ($this->transactionCount < 1) {
            return;
        }
        $transaction = $this->unprepared("ROLLBACK;");
        if (false !== $transaction) {
            $this->transactionCount--;
        }
    }

    /**
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel()
    {
        return $this->transactionCount;
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        global $wpdb;

        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        $capsule = new Capsule;

        $dbuser     = defined('DB_USER') ? DB_USER : '';
        $dbpassword = defined('DB_PASSWORD') ? DB_PASSWORD : '';
        $dbname     = defined('DB_NAME') ? DB_NAME : '';
        $dbhost     = defined('DB_HOST') ? DB_HOST : '';
        $charset    = $wpdb->charset;
        $collate    = $wpdb->collate;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $dbhost,
            'database'  => $dbname,
            'username'  => $dbuser,
            'password'  => $dbpassword,
            'charset'   => $charset,
            'collation' => $collate,
            'prefix'    => $wpdb->base_prefix,
        ], 'default');

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return new SchemaBuilder($capsule->getConnection());
    }

    /**
     * Set the schema grammar to the default implementation.
     *
     * @return void
     */
    public function useDefaultSchemaGrammar()
    {
        $this->schemaGrammar = $this->getDefaultSchemaGrammar();
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->getQueryGrammar();
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param \Closure $callback
     *
     * @return array
     */
    public function pretend(\Closure $callback)
    {
        return $this->withFreshQueryLog(function () use ($callback) {
            $this->pretending = true;

            // Basically to make the database connection "pretend", we will just return
            // the default values for all the query methods, then we will return an
            // array of queries that were "executed" within the Closure callback.
            $callback($this);

            $this->pretending = false;

            return $this->queryLog;
        });
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param \Closure $callback
     * @return array
     */
    protected function withFreshQueryLog($callback)
    {
        $loggingQueries = $this->loggingQueries;

        // First we will back up the value of the logging queries property and then
        // we'll be ready to run callbacks. This query log will also get cleared
        // so we will have a new log of all the queries that are executed now.
        $this->enableQueryLog();

        $this->queryLog = [];

        // Now we'll execute this callback and capture the result. Once it has been
        // executed we will restore the value of query logging and give back the
        // value of the callback so the original callers can have the results.
        $result = $callback();

        $this->loggingQueries = $loggingQueries;

        return $result;
    }

    /**
     * Get the connection query log.
     *
     * @return array
     */
    public function getQueryLog()
    {
        return $this->queryLog;
    }

    /**
     * Clear the query log.
     *
     * @return void
     */
    public function flushQueryLog()
    {
        $this->queryLog = [];
    }

    /**
     * Enable the query log on the connection.
     *
     * @return void
     */
    public function enableQueryLog()
    {
        $this->loggingQueries = true;
    }

    /**
     * Disable the query log on the connection.
     *
     * @return void
     */
    public function disableQueryLog()
    {
        $this->loggingQueries = false;
    }

    /**
     * Determine whether we're logging queries.
     *
     * @return bool
     */
    public function logging()
    {
        return $this->loggingQueries;
    }


    public function getPostProcessor()
    {
        return new Processor();
    }

    public function getQueryGrammar()
    {
        return new QueryGrammar();
    }


    /**
     * Return the last insert id
     *
     * @param string $args
     *
     * @return int
     */
    public function lastInsertId($args)
    {
        return $this->db->insert_id;
    }

    /**
     * Get an option from the configuration options.
     *
     * @param string|null $option
     * @return mixed
     */
    public function getConfig($option = null)
    {
        return Arr::get($this->config, $option);
    }

    /**
     * Get the name of the connected database.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return defined('DB_NAME') ? DB_NAME : '';
    }

    /**
     * Make sure that the given table name is prefixed,
     * otherwise prefix it, then return the table name.
     *
     * @param string $table_name
     * @return string
     */
    public function prefixTable($table_name)
    {
        $prefix = $this->db->prefix ?: '';

        return ($prefix && (strpos($table_name, $prefix) !== 0))
                ? $prefix . $table_name
                : $table_name;
    }
}
