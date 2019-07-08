<?php

namespace UnderScorer\ORM\Models;

/**
 * Class Attachment
 * @package UnderScorer\ORM\Models
 *
 * @property string alt
 */
class Attachment extends Post
{

    /**
     * @var array
     */
    protected static $aliases = [
        'title'       => 'post_title',
        'url'         => 'guid',
        'type'        => 'post_mime_type',
        'description' => 'post_content',
        'caption'     => 'post_excerpt',
        'alt'         => [ 'meta' => '_wp_attachment_image_alt' ],
    ];

    /**
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * @var array
     */
    protected $appends = [
        'title',
        'url',
        'type',
        'description',
        'caption',
        'alt',
    ];

}
