<?php

namespace UnderScorer\ORM\WP;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class Post
 *
 * @package UnderScorer\ORM\WP
 */
class Post extends Model {

    use WithMeta;

    /**
     * @var string
     */
    const CREATED_AT = 'post_date';

    /**
     * @var string
     */
    const UPDATED_AT = 'post_modified';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var string
     */
    protected $metaRelation = PostMeta::class;

    /**
     * @var string
     */
    protected $metaForeignKey = 'post_id';

    /**
     * @var array
     */
    protected $attributes = [
        'post_type' => 'post',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'post_title',
        'post_content',
        'post_author',
        'post_type',
    ];

    /**
     * Filter by post type
     *
     * @param        $query
     * @param string $type
     *
     * @return mixed
     */
    public function scopeType( $query, $type = 'post' ) {
        return $query->where( 'post_type', '=', $type );
    }

    /**
     * Filter by post status
     *
     * @param        $query
     * @param string $status
     *
     * @return mixed
     */
    public function scopeStatus( $query, $status = 'publish' ) {
        return $query->where( 'post_status', '=', $status );
    }

    /**
     * Filter by post author
     *
     * @param      $query
     * @param null $author
     *
     * @return mixed
     */
    public function scopeAuthor( $query, $author = null ) {
        if ( $author ) {
            return $query->where( 'post_author', '=', $author );
        }

        return null;
    }

    /**
     * Get comments from the post
     *
     * @return HasMany
     */
    public function comments() {
        return $this->hasMany( Comment::class, 'comment_post_ID' );
    }

    /**
     * @param string $taxonomy
     *
     * @return TermTaxonomy[] | Collection
     */
    public function taxonomy( string $taxonomy ) {
        return $this->taxonomies()->where( 'taxonomy', '=', $taxonomy )->get();
    }

    /**
     * @return BelongsToMany
     */
    public function taxonomies() {

        $pivotTable = $this->getConnection()->db->prefix . 'term_relationships';

        return $this->belongsToMany( TermTaxonomy::class, $pivotTable, 'object_id', 'term_taxonomy_id' );

    }

    /**
     * Attaches provided array of terms into post instance
     *
     * @param string $taxonomy
     * @param array  $terms Array of terms with "name" and "slug" keys
     *
     * @return void
     */
    public function addTerms( string $taxonomy, array $terms ) {

        foreach ( $terms as $term ) {

            $name = $term[ 'name' ];
            $slug = $term[ 'slug' ];

            /**
             * @var Term $term
             */
            $term = Term::query()->firstOrCreate( [
                'name' => $name,
                'slug' => $slug,
            ] );

            /**
             * @var TermTaxonomy $termTaxonomy Relation between term and taxonomy
             */
            $termTaxonomy = TermTaxonomy::query()->firstOrCreate(
                [
                    'term_taxonomy_id' => $term->term_id,
                    'term_id'          => $term->term_id,
                    'taxonomy'         => $taxonomy,
                ]
            );

            // Attach created term taxonomy into post instance
            $this->taxonomies()->attach( $termTaxonomy );

        }

    }

}
