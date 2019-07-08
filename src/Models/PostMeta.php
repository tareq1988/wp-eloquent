<?php

namespace UnderScorer\ORM\Models;

use UnderScorer\ORM\Contracts\MetaInterface;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class PostMeta
 * @package UnderScorer\ORM\WP
 *
 * @property string meta_key
 * @property mixed  meta_value
 */
class PostMeta extends Model implements MetaInterface
{

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * @var array
     */
    protected $fillable = [
        'meta_key',
        'meta_value',
        'post_id'
    ];

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->postmeta;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->meta_value;
    }

    /**
     * @param mixed $value
     *
     * @return static
     */
    public function setMetaValue( $value )
    {
        $this->meta_value = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return static
     */
    public function setMetaKey( string $key )
    {
        $this->meta_key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKey(): string
    {
        return $this->meta_key;
    }

    /**
     * @param $value
     *
     * @return void
     */
    public function setMetaValueAttribute( $value )
    {
        $this->attributes[ 'meta_value' ] = maybe_serialize( $value );
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getMetaValueAttribute( $value )
    {
        return maybe_unserialize( $value );
    }

}
