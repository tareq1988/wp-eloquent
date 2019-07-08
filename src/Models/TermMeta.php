<?php

namespace UnderScorer\ORM\Models;

/**
 * Class TermMeta
 * @package UnderScorer\ORM\Models
 */
class TermMeta extends PostMeta
{

    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * @var array
     */
    protected $fillable = [
        'meta_key',
        'meta_value',
        'term_id'
    ];

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->termmeta;
    }

}
