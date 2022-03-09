<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Concerns\AdvancedCustomFields;
use AmphiBee\Eloquent\Concerns\Aliases;
use AmphiBee\Eloquent\Concerns\MetaFields;
use AmphiBee\Eloquent\Concerns\OrderScopes;
use AmphiBee\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;

/**
 * Class User
 *
 * @package AmphiBee\Eloquent
 * @author Ashwin Sureshkumar <ashwin.sureshkumar@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class User extends Model implements Authenticatable, CanResetPassword
{
    const CREATED_AT = 'user_registered';
    const UPDATED_AT = null;

    use AdvancedCustomFields;
    use Aliases;
    use MetaFields;
    use OrderScopes;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var array
     */
    protected $hidden = ['user_pass'];

    /**
     * @var array
     */
    protected $dates = ['user_registered'];

    /**
     * @var array
     */
    protected $with = ['meta'];

    /**
     * @var array
     */
    protected static $aliases = [
        'id'          => 'ID',
        'login'       => 'user_login',
        'email'       => 'user_email',
        'slug'        => 'user_nicename',
        'url'         => 'user_url',
        'nickname'    => ['meta' => 'nickname'],
        'first_name'  => ['meta' => 'first_name'],
        'last_name'   => ['meta' => 'last_name'],
        'description' => ['meta' => 'description'],
        'created_at'  => 'user_registered',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'login',
        'email',
        'slug',
        'url',
        'nickname',
        'first_name',
        'last_name',
        'avatar',
        'created_at',
    ];

    /**
     * @param mixed $value
     */
    public function setUpdatedAtAttribute($value)
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_author');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->primaryKey];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_pass;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        $tokenName = $this->getRememberTokenName();

        return $this->meta->{$tokenName};
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $tokenName = $this->getRememberTokenName();

        $this->saveMeta($tokenName, $value);
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->user_email;
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
    }

    /**
     * Get the avatar url from Gravatar
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        $hash = !empty($this->email) ? md5(strtolower(trim($this->email))) : '';

        return sprintf('//secure.gravatar.com/avatar/%s?d=mm', $hash);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAt($value)
    {
        //
    }

    /**
     * Get the currently logged in user, return null if no user is logged.
     *
     * @return User|null
     */
    public static function current()
    {
        if (($id = get_current_user_id())) {
            return static::find($id);
        }

        return null;
    }
}
