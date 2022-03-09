<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Concerns\Aliases;

/**
 * Class Attachment
 *
 * @package AmphiBee\Eloquent\Model
 * @author José CI <josec89@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class Attachment extends Post
{
    use Aliases;

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

    /**
     * @var array
     */
    protected static $aliases = [
        'title'       => 'post_title',
        'url'         => 'guid',
        'type'        => 'post_mime_type',
        'description' => 'post_content',
        'caption'     => 'post_excerpt',
    ];



    /******************************************/
    /*                                        */
    /*        WordPress related methods       */
    /*                                        */
    /******************************************/


    /**
     * Get the alt attribute for the attachment.
     */
    public function getAltAttribute(): string
    {
        return $this->meta->_wp_attachment_image_alt ?: '';
    }

    /**
     * Get the attachment URL
     */
    public function getPermalinkAttribute()
    {
        return $this->getAttachmentUrl();
    }
    
    /**
     * Get the attachment URL
     */
    public function getUrlAttribute()
    {
        return $this->getAttachmentUrl();
    }

    /**
     * Get the attachment URL
     */
    public function getAttachmentUrl($size = ''): string
    {
        return (
            !empty($size) ? wp_get_attachment_image_url($this->id, $size) : wp_get_attachment_url($this->id)
        ) ?: '';
    }
}
