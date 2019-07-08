<?php

namespace UnderScorer\ORM\Models;


use Illuminate\Support\Arr;

/**
 * Class ThumbnailMeta
 * @package UnderScorer\ORM\Models
 *
 * @property Attachment attachment
 */
class ThumbnailMeta extends PostMeta
{
    /**
     * @var string
     */
    const SIZE_THUMBNAIL = 'thumbnail';

    /**
     * @var string
     */
    const SIZE_MEDIUM = 'medium';

    /**
     * @var string
     */
    const SIZE_LARGE = 'large';

    /**
     * @var string
     */
    const SIZE_FULL = 'full';

    /**
     * @var array
     */
    protected $with = [ 'attachment' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attachment()
    {
        return $this->belongsTo( Attachment::class, 'meta_value' );
    }

    /**
     * @param string $size
     *
     * @return array
     * @throws \Exception
     */
    public function size( $size )
    {
        if ( $size == self::SIZE_FULL ) {
            return $this->attachment->url;
        }
        $meta  = unserialize( $this->attachment->meta->_wp_attachment_metadata );
        $sizes = Arr::get( $meta, 'sizes
        ' );
        if ( ! isset( $sizes[ $size ] ) ) {
            return $this->attachment->url;
        }

        $data = Arr::get( $sizes, $size );

        return array_merge( $data, [
            'url' => dirname( $this->attachment->url ) . '/' . $data[ 'file' ],
        ] );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->attachment->guid;
    }
}
