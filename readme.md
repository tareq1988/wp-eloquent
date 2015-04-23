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

$db = \WeDevs\Eloquent\Database::instance();

var_dump( $db->table('users')->find(1) );
var_dump( $db->select('SELECT * FROM wp_users WHERE id = ?', [1]) );
var_dump( $db->table('users')->where('user_login', 'john')->first() );
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
use \WeDevs\Eloquent\Model as Model;

class Post extends Model {
    protected $primaryKey = 'ID';
}

var_dump( Post::all()->toArray() ); // gets all posts
var_dump( Post::find(1) ); // find posts with ID 1
```
The class name `Post` will be translated into `PREFIX_posts` table to run queries. But as usual, you can override the table name.

### Writing a Model for a WordPress post_type

```php
use \WeDevs\Eloquent\Model as BaseModel;

class BasePostType extends Model {
	/**
	 * string The wordpress post_type (optional)
	 */
	protected $wp_post_type = null;
    protected $primaryKey = 'ID';
    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    /**
     * Extends newQuery to always filter for the model's wordpress post_type
     */
    public function newQuery(){
    	$q = parent::newQuery();
    	if (!empty($this->wp_post_type)){
        	$q->where("post_type",$this->wp_post_type);
    	}
    	return $q;
    }
}
```

```php
class Post extends BasePostType {
    protected $wp_post_type = "post";
}

var_dump( Post::all() ); //returns only posts with wordpress post_type "post"
```

```php
class MyCustomPostType extends BasePostType {
    protected $wp_post_type = "my_custom_post_type";
}
var_dump( MyCustomPostType::all() ); //returns only posts with wordpress post_type "my_custom_post_type"
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