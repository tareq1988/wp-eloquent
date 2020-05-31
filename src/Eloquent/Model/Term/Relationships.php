<?php

namespace AmphiBee\Eloquent\Model\Term;

use AmphiBee\Eloquent\Model\BaseModel;

/**
 * Class Relashionships
 *
 * @package AmphiBee\Eloquent\Model\Term
 */
class Relationships extends BaseModel
{
    /** @var string */
    protected $table = 'term_relationships';
    
    /** @var string */
    protected $primaryKey = 'term_taxonomy_id';
}
