<?php

namespace AmphiBee\Eloquent\Model\Meta;

use AmphiBee\Eloquent\Model\Term;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TermMeta
 *
 * @package AmphiBee\Eloquent\Model\Meta
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermMeta extends Meta
{
    /** @var string */
    protected $table = 'termmeta';

    /** @var string */
    protected $primaryKey = 'meta_id';

    /** @var array */
    protected $fillable = ['meta_key', 'meta_value', 'term_id'];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
