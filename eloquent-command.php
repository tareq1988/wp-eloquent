<?php
/**
 * Implements eloquent command.
 */
class Eloquent_Command extends WP_CLI_Command
{

    public function __construct()
    {
        $dir = plugin_dir_path(__FILE__) . '../../../../migrations/';
        $this->migrationDir = $dir = realpath($dir);

        $this->migrator = new \WeDevs\ORM\Eloquent\Migrator($dir);
    }

    /**
     * Run the migration commands.
     *
     * ## OPTIONS
     *
     *
     * ## EXAMPLES
     *
     *     wp eloquent migrate
     *
     * @synopsis [--reset] [--refresh]
     */
    public function migrate($args, $assoc_args)
    {
        $reset = $assoc_args['reset'] ?: false;
        $refresh = $assoc_args['refresh'] ?: false;

        if ($reset) {
            $this->migrator->runDown();
        } elseif ($refresh) {
            $this->migrator->refresh();
        } else {
            $this->migrator->runUp();
        }
    }

    /**
     * Run the migration commands.
     *
     * ## OPTIONS
     *
     *
     * ## EXAMPLES
     *
     *     wp eloquent make
     *
     * @synopsis <name> [--create=<table>] [--table=<table>]
     */
    public function make($args, $assoc_args)
    {
        list($name) = $args;
        $class_name = ucwords(str_replace("_", " ", strtolower($name)));
        $class_name = str_replace(" ", "", $class_name);

        $table = $assoc_args['table'] ?: null;
        $create = $assoc_args['create'] ?: null;

        if (!$table && is_string($create)) {
            $table = $create;
        }

        $date_prefix = date('Y_m_d_His');

        if (is_null($table)) {
            $file_data = file_get_contents(__DIR__ . '/Eloquent/stubs/blank.stub');

            $file_data = str_replace("DummyClass", $class_name, $file_data);

            return file_put_contents($this->migrationDir . '/' . $date_prefix . '_' . $name . '.php', $file_data);
        } else {
            $stub = $create ? 'create.stub' : 'update.stub';

            $file_data = file_get_contents(__DIR__ . '/Eloquent/stubs/' . $stub);

            $file_data = str_replace("DummyClass", $class_name, $file_data);
            $file_data = str_replace("DummyTable", $table, $file_data);

            return file_put_contents($this->migrationDir . '/' . $date_prefix . '_' . $name . '.php', $file_data);
        }
    }
}

WP_CLI::add_command('eloquent', 'Eloquent_Command');
