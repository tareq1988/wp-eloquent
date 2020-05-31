<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Traits\HasMeta;

/**
 * Class Term
 *
 * @package AmphiBee\Eloquent\Model
 */
class Term extends BaseModel
{
    use HasMeta;

    /** @var string */
    protected $table = 'terms';

    /** @var string */
    protected $primaryKey = 'term_id';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function meta()
    {
        return $this->hasMany(Term\Meta::class, 'term_id')
                    ->select(['term_id', 'meta_key', 'meta_value']);
    }
}
