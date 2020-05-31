<?php

namespace AmphiBee\Eloquent\Model\Term;

use AmphiBee\Eloquent\Model\BaseModel;

/**
 * Class Taxonomy
 *
 * @package AmphiBee\Eloquent\Model\Term
 */
class Taxonomy extends BaseModel
{
    /** @var string */
    protected $table = 'term_taxonomy';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function term()
    {
        return $this->belongsTo(\AmphiBee\Eloquent\Model\Term::class, 'term_id', 'term_id');
    }
}
