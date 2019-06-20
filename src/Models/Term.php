<?php

namespace UnderScorer\ORM\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class Term
 * @package WPK\Core\Models\WP
 *
 * @property int          term_id
 * @property string       name
 * @property string       slug
 * @property int          term_group
 * @property TermTaxonomy taxonomy
 */
class Term extends Model
{

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * @return BelongsTo
     */
    public function taxonomy()
    {
        return $this->belongsTo( TermTaxonomy::class, 'term_id' );
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->prefix . 'terms';
    }

}
