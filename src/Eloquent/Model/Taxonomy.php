<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Model;
use AmphiBee\Eloquent\Connection;
use AmphiBee\Eloquent\Concerns\Aliases;
use AmphiBee\Eloquent\Model\Meta\TermMeta;
use AmphiBee\Eloquent\Model\Builder\TaxonomyBuilder;

/**
 * Class Taxonomy
 *
 * @package AmphiBee\Eloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class Taxonomy extends Model
{
    use Aliases;

    /**
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * @var array
     */
    protected $with = ['term'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected static $aliases = [
        'name' => 'taxonomy',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Taxonomy::class, 'parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            (new Connection)->pdo->prefix() . 'term_relationships', # put prefix here to prevent issue
            'term_taxonomy_id',
            'object_id'
        );
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return TaxonomyBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new TaxonomyBuilder($query);
    }

    /**
     * @return TaxonomyBuilder
     */
    public function newQuery()
    {
        return isset($this->taxonomy) && $this->taxonomy ?
            parent::newQuery()->where('taxonomy', $this->taxonomy) :
            parent::newQuery();
    }

    /**
     * Magic method to return the meta data like the post original fields.
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        if (!isset($this->$key)) {
            if (isset($this->term->$key)) {
                return $this->term->$key;
            }
        }

        return parent::__get($key);
    }
    
    /**
     * Creates a query builder which get the term neighbors
     * in hierarchy for this term (same parent).
     *
     * @return QueryBuilder
     */
    public function neighbors()
    {
        $parent  = $this->parent;
        $exclude = $this->term_id;

        $query = static::where('term_id', '!=', $exclude);

        return $parent ? $query->where('parent', $parent) : $query->whereNull('parent');
    }


    
    /******************************************/
    /*                                        */
    /*               WP methods               */
    /*                                        */
    /******************************************/


    /**
     * Get the taxonomy labels.
     *
     * @return stdObject The labels
     */
    public function getLabelsAttribute()
    {
        return (get_taxonomy($this->name))->labels;
    }



    /******************************************/
    /*                                        */
    /*             Query builders             */
    /*                                        */
    /******************************************/


    /**
     * Get current taxonomy using wordpress magic function
     *
     * @return Model null on failure
     * @since 1.0.0
     */
    public static function current()
    {
        $id = get_queried_object_id();

        if (!$id) {
            return null;
        }

        return static::find($id);
    }
}