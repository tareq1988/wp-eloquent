<?php

namespace UnderScorer\ORM\Traits;

use Illuminate\Support\Arr;
use UnderScorer\ORM\Eloquent\Model;

/**
 * @mixin Model
 */
trait Aliases
{
    /**
     * @param string $new
     * @param string $old
     */
    public static function addAlias( $new, $old )
    {
        static::$aliases[ $new ] = $old;
    }

    /**
     * Get alias value from mutator or directly from attribute
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function mutateAttribute( $key, $value )
    {
        if ( $this->hasGetMutator( $key ) ) {
            return parent::mutateAttribute( $key, $value );
        }

        return $this->getAttribute( $key );
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute( $key )
    {
        $value = parent::getAttribute( $key );
        if ( $value === null && count( static::getAliases() ) ) {
            if ( $value = Arr::get( static::getAliases(), $key ) ) {
                if ( is_array( $value ) ) {
                    $meta = Arr::get( $value, 'meta' );

                    return $meta ? $this->meta->$meta : null;
                }

                return parent::getAttribute( $value );
            }
        }

        return $value;
    }

    /**
     * @return array
     */
    public static function getAliases()
    {
        if ( isset( parent::$aliases ) && count( parent::$aliases ) ) {
            return array_merge( parent::$aliases, static::$aliases );
        }

        return static::$aliases;
    }
}
