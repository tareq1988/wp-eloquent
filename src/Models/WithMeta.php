<?php

namespace UnderScorer\ORM\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use UnderScorer\ORM\Contracts\MetaInterface;

/**
 * @method HasMany hasMany( string $related, string $foreignKey = null, string $localKey = null )
 * @property string $metaRelation
 * @property string $metaForeignKey
 */
trait WithMeta
{

    /**
     * Returns single meta value
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getSingleMeta( string $key )
    {
        return $this
            ->meta()
            ->where( 'meta_key', '=', $key )
            ->pluck( 'meta_value' )
            ->first();
    }

    /**
     * @return HasMany
     */
    public function meta()
    {
        return $this->hasMany( $this->metaRelation, $this->metaForeignKey );
    }

    /**
     * Adds new meta value to model
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return MetaInterface
     */
    public function addMeta( string $key, $value ): MetaInterface
    {
        /**
         * @var MetaInterface $meta
         */
        $meta = $this->meta()->create( [
            'meta_key'   => $key,
            'meta_value' => $value,
        ] );

        return $meta;
    }

    /**
     * Fetches metas basing on key
     *
     * @param string $key
     *
     * @return Collection|MetaInterface[]
     */
    public function getMeta( string $key )
    {
        return $this
            ->meta()
            ->where( 'meta_key', '=', $key )
            ->get();
    }

    /**
     * Updates meta value
     *
     * @param string $key
     * @param        $value
     * @param null   $prevValue
     *
     * @return bool|false|\Illuminate\Database\Eloquent\Model
     */
    public function updateMeta( string $key, $value, $prevValue = null )
    {
        $attributes = [
            'meta_key' => $key,
        ];

        if ( ! is_null( $prevValue ) ) {
            $attributes[ 'meta_value' ] = $prevValue;
        }

        /**
         * @var MetaInterface $meta
         */
        $meta = $this->meta()->firstOrCreate( $attributes );

        if ( empty( $meta ) ) {
            $meta = $this->meta()->create( [
                'meta_key' => $key,
            ] );
        }

        if ( ! $meta ) {
            return false;
        }

        $meta->setMetaValue( $value );

        return $meta->save();
    }

    /**
     * @param string $metaKey
     * @param null   $value
     *
     * @return mixed
     */
    public function deleteMeta( string $metaKey, $value = null )
    {
        $query = $this->meta()->where('meta_key', '=', $metaKey);

        if(!is_null($value)){
            $query->where('meta_value', '=', $value);
        }

        return $query->delete();
    }

}
