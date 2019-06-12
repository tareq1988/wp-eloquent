<?php

namespace WeDevs\ORM\WP;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @method HasMany hasMany( string $related, string $foreignKey = null, string $localKey = null )
 * @property string $metaRelation
 * @property string $metaForeignKey
 */
trait WithMeta {

    /**
     * @var string
     */
    protected $metaRelation;

    /**
     * @var string
     */
    protected $metaForeignKey;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getSingleMeta( string $key ) {

        return $this
            ->meta()
            ->where( 'meta_key', '=', $key )
            ->limit( 1 )
            ->get()
            ->first();

    }

    /**
     * @return HasMany
     */
    public function meta() {
        return $this->hasMany( $this->metaRelation, $this->metaForeignKey );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function addMeta( string $key, $value ) {

        $meta = $this->meta()->create( [
            'meta_key'   => $key,
            'meta_value' => $value,
        ] );

        return $meta;

    }

    /**
     * @param string $key
     *
     * @return Collection
     */
    public function getMeta( string $key ) {

        return $this
            ->meta()
            ->where( 'meta_key', '=', $key )
            ->get();

    }

}
