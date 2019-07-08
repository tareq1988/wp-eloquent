<?php

namespace UnderScorer\ORM\Models;

use UnderScorer\ORM\Collections\MetaCollection;
use UnderScorer\ORM\Contracts\MetaInterface;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class Meta
 * @package UnderScorer\ORM\Models
 * @property string meta_key
 * @property mixed  meta_value
 */
abstract class Meta extends Model implements MetaInterface
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
     * @return string
     */
    public function getTable()
    {
        throw new \RuntimeException(
            sprintf( "Method %s:%s must be implemented in child class %s.", self::class, __METHOD__, static::class )
        );
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

    /**
     * @param array $models
     *
     * @return MetaCollection
     */
    public function newCollection( array $models = [] )
    {
        return MetaCollection::make( $models );
    }

}
