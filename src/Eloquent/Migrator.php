<?php

namespace WeDevs\ORM\Eloquent;

use Illuminate\Support\Str;

class Migrator
{
    /**
     * Migration Directory
     *
     * @var $migrationDir
     */
    protected $migrationDir = null;

    /**
     * Class construction.
     */
    public function __construct($migrationDir)
    {
        $this->migrationDir = $migrationDir;
    }
    /**
     * Get all of the migration files in a given path.
     *
     * @param  string  $path
     *
     * @return array
     */
    protected function getMigrationFiles($path)
    {
        $files = [];

        foreach (glob($path . '/*_*.php') as $filename) {
            $files[] = $filename;
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);

        sort($files);

        return $files;
    }

    /**
     * Get migrated migration list from json file.
     *
     * @return array
     */
    protected function getMigrationList()
    {
        $migrationJsonFile = $this->migrationDir . '/migrations.json';

        if (!file_exists($migrationJsonFile)) {
            file_put_contents($migrationJsonFile, json_encode([]));
        }

        $migrationJson = file_get_contents($migrationJsonFile);
        $migrationList = json_decode($migrationJson);

        return $migrationList;
    }

    /**
     * Save migrated migration list to json file.
     *
     * @return boolean
     */
    protected function saveMigrationListToFile($newMigrationList)
    {
        return file_put_contents($this->migrationDir . '/migrations.json', json_encode($newMigrationList));
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     *
     * @return object
     */
    protected function resolve($file)
    {
        include_once $this->migrationDir . '/' . $file . '.php';

        $file = implode('_', array_slice(explode('_', $file), 4));
        $class = Str::studly($file);

        return new $class;
    }

    /**
     * Call migration up method.
     *
     * @return void
     */
    public function runUp()
    {
        $migrations = $this->getMigrationFiles($this->migrationDir);

        $migrationList = $this->getMigrationList();

        $migrated = [];
        foreach ($migrations as $migration) {
            $file = $this->migrationDir . '/' . $migration . '.php';

            if (file_exists($file) && !in_array($migration . '.php', $migrationList)) {
                $instance = $this->resolve($migration);
                $instance->up();

                $migrated[] = $migration . '.php';
            }
        }

        if (!empty($migrated)) {
            $newMigrationList = array_merge($migrationList, $migrated);
            $this->saveMigrationListToFile($newMigrationList);
            //echo "Migrated:  $migration.php \n";
        } else {
            echo "Nothing to migrate.";
        }
    }

    /**
     * Call migration down method.
     *
     * @return void
     */
    public function runDown()
    {
        $migrations = $this->getMigrationFiles($this->migrationDir);

        $migrationList = $this->getMigrationList();

        $migrated = [];
        foreach ($migrations as $migration) {
            $file = $this->migrationDir . '/' . $migration . '.php';

            if (file_exists($file)) {
                $instance = $this->resolve($migration);
                $instance->down();

                $migrated[] = $migration . '.php';
            }
        }

        if (!empty($migrated)) {
            $newMigrationList = array_diff($migrationList, $migrated);
            $this->saveMigrationListToFile($newMigrationList);
            //echo "Migrated:  $migration.php \n";
        } else {
            echo "Nothing to migrate.";
        }
    }

    /**
     * Refresh migrations.
     *
     * @return void
     */
    public function refresh()
    {
        $this->runDown();

        $this->runUp();
    }
}
