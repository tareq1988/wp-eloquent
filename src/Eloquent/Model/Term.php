<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Concerns\AdvancedCustomFields;
use AmphiBee\Eloquent\Concerns\MetaFields;
use AmphiBee\Eloquent\Model;

/**
 * Class Term.
 *
 * @package AmphiBee\Eloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Term extends Model
{
    use MetaFields;
    use AdvancedCustomFields;

    /**
     * @var string
     */
    protected $table = 'terms';

    /**
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }
}
