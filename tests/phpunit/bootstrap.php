<?php

namespace WPK\Tests;

use Dotenv\Dotenv;

define( 'TESTS_DIR', __DIR__ );

$dir = __DIR__;

require_once $dir . '/../../vendor/autoload.php';

$testsDir = $dir . '/Suite/wordpress-tests-lib';

if ( ! file_exists( $testsDir ) ) {
    $dotenv = Dotenv::create( $dir );
    $dotenv->load();

    $testsDir = getenv( 'WP_TESTS_DIR' );

    if ( ! $testsDir ) {
        echo 'Error! You need to either setup tests suite using docker-compose or provide path to your own tests suite in WP_TESTS_DIR env variable.';

        return;
    }

}

// Disable revisions
define( 'WP_POST_REVISIONS', false );

// Give access to tests_add_filter() function.
require_once $testsDir . '/includes/functions.php';

// disable xdebug backtrace
if ( function_exists( 'xdebug_disable' ) ) {
    xdebug_disable();
}

// Start up the WP testing environment.
require $testsDir . '/includes/bootstrap.php';


