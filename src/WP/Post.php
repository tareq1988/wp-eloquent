<?php

namespace UnderScorer\ORM\WP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class Post
 *
 * @package UnderScorer\ORM\WP
 *
 * @property int    post_author
 * @property string post_title
 * @property string post_content
 * @property string post_excerpt
 * @property string comment_status
 * @property string post_status
 * @property string post_type
 * @property string post_content_filtered
 * @property string post_parent
 * @property string guid
 * @property string post_mime_type
 * @property string comment_count
 * @property int    menu_order
 * @property Carbon post_date
 * @property Carbon post_date_gmt
 * @property Carbon post_modified
 * @property Carbon post_modified_gmt
 * @property User author
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
    protected $dates = [
        'post_date',
        'post_date_gmt',
        'post_modified',
        'post_modified_gmt',
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

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo {
        return $this->belongsTo( User::class, 'post_author' );
    }

}
