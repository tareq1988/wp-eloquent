<?php

namespace AmphiBee\Eloquent\Model;

/**
 * Tag class.
 *
 * @package AmphiBee\Eloquent\Model
 * @author Mickael Burguet <www.rundef.com>
 */
class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'post_tag';
}
