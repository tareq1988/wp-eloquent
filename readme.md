# Eloquent Wrapper for WordPress

This is a library package to use Laravel's [Eloquent ORM](http://laravel.com/docs/5.0/eloquent) with WordPress.


## Package Installation

To install this package, edit your `composer.json` file:

```js
{
    "require": {
        "tareq1988/wp-eloquent": "dev-master"
    }
}
```

Now run:

`$ composer install`

# Usage Example

## Basic Usage

```php

$db = \WeDevs\ORM\Eloquent\Database::instance();

var_dump( $db->table('users')->find(1) );
var_dump( $db->select('SELECT * FROM wp_users WHERE id = ?', [1]) );
var_dump( $db->table('users')->where('user_login', 'john')->first() );

// OR with DB facade
use \WeDevs\ORM\Eloquent\Facades\DB;

var_dump( DB::table('users')->find(1) );
var_dump( DB::select('SELECT * FROM wp_users WHERE id = ?', [1]) );
var_dump( DB::table('users')->where('user_login', 'john')->first() );
```

## Creating Models For Custom Tables
You can use custom tables of the WordPress databases to create models:

```
	namespace whatever;


	class CustomTableModel extends \WeDevs\ORM\Eloquent\Model {

		/**
		 * Name for table without prefix
		 *
		 * @var string
		 */
		protected $table = 'table_name';


		/**
		 * Columns that can be edited - IE not primary key and timestamps if being uses
		 */
		protected $fillable = [
			'city',
			'state',
			'country'
		];

		/**
		 * Disable created_at and update_at columns, unless you have those.
		 */
		public $timestamps = false;

		/** Everything below this is best done in an abstract class that custom tables extend */

		/**
		 * Set primary key as ID, because WordPress
		 *
		 * @var string
		 */
		protected $primaryKey = 'ID';

		/**
		 * Make ID guarded -- without this ID doesn't save.
		 *
		 * @var string
		 */
		protected $guarded = [ 'ID' ];

		/**
		 * Overide parent method to make sure prefixing is correct.
		 *
		 * @return string
		 */
		public function getTable()
		{
			//In this example, it's set, but this is better in an abstract class
			if( isset( $this->table ) ){
				$prefix =  $this->getConnection()->db->prefix;
				return $prefix . $this->table;

			}

			return parent::getTable();
		}

	}
```

### Retrieving All Rows From A Table

```php
$users = $db->table('users')->get();

foreach ($users as $user) {
    var_dump($user->display_name);
}
```

Here `users` is the table name **without prefix**. The prefix will be applied automatically.


### Other Examples

 - [Queries](http://laravel.com/docs/5.0/queries)
 - [Eloquent ORM](http://laravel.com/docs/5.0/eloquent)

## Writing a Model

```php
use \WeDevs\ORM\Eloquent\Model as Model;

class Employee extends Model {

}

var_dump( Employee::all()->toArray() ); // gets all employees
var_dump( Employee::find(1) ); // find employee with ID 1
```
The class name `Employee` will be translated into `PREFIX_employees` table to run queries. But as usual, you can override the table name.

### In-built Models for WordPress

- Post
- Comment
- Post Meta
- User
- User Meta


```php
use WeDevs\ORM\WP\Post as Post;

var_dump( Post::all() ); //returns only posts with WordPress post_type "post"
```

#### Filter `Post` by `post_status` and `post_type`
```php
use WeDevs\ORM\WP\Post as Post;
var_dump(Post::type('page')->get()->toArray()); // get pages
var_dump(Post::status('publish')->get()->toArray()); // get posts with publish status
var_dump(Post::type('page')->status('publish')->get()->toArray()); // get pages with publish status
```

## How it Works

 - Eloquent is mainly used here as the query builder
 - [WPDB](http://codex.wordpress.org/Class_Reference/wpdb) is used to run queries built by Eloquent
 - Hence, we have the benfit to use plugins like `debug-bar` or `query-monitor` to get SQL query reporting.
 - It doesn't create any extra MySQL connection


## Minimum Requirement
 - PHP 5.3.0
 - WordPress 3.6+

## Author
[Tareq Hasan](http://tareq.wedevs.com)
