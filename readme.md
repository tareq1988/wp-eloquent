# Eloquent for WordPress

This is a library package to use Laravel's [Eloquent ORM](http://laravel.com/docs/5.0/eloquent) with WordPress.

# Usage Example

## Raw Query

```php
use \WeDevs\Eloquent\Database as DB;

$db = new DB();
var_dump( $db->table('users')->find(1) );
```

Here `users` is the table name **without prefix**. The prefix will be applied automatically.

## Writing a Model

```php
use \WeDevs\Eloquent\Model as Model;

class Post extends Model {
    protected $primaryKey = 'ID';
}

var_dump( Post::all()->toArray() ); // gets all posts
var_dump( Post::find(1) ); // find posts with ID 1
```

The class name `Post` will be translated into `PREFIX_posts` table to run queries. But as usual, you can override the table name.

## How it Works

 - Eloquent is mainly used here as the query builder
 - [WPDB](http://codex.wordpress.org/Class_Reference/wpdb) is used to run queries built by Eloquent
 - Hence, we have the benfit to use plugins like `debug-bar` or `query-monitor` to get SQL query reporting.
 - It doesn't create any extra MySQL connection


---

## Minimum Requirement
 - PHP 5.3.0
 - WordPress 3.6+

## Author
[Tareq Hasan](http://tareq.wedevs.com)