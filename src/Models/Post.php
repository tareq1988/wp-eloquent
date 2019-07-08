<?php

namespace UnderScorer\ORM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use UnderScorer\ORM\Builders\PostBuilder;
use UnderScorer\ORM\Eloquent\Model;
use UnderScorer\ORM\Traits\Aliases;
use WP_Post;

/**
 * Class Post
 *
 * @package UnderScorer\ORM\WP
 *
 * @property int           ID
 * @property int           post_author
 * @property string        post_title
 * @property string        title
 * @property string        post_content
 * @property string        content
 * @property string        post_excerpt
 * @property string        comment_status
 * @property string        post_status
 * @property string        post_type
 * @property string        post_content_filtered
 * @property string        post_parent
 * @property string        guid
 * @property string        post_mime_type
 * @property string        comment_count
 * @property int           menu_order
 * @property Carbon        post_date
 * @property Carbon        post_date_gmt
 * @property Carbon        post_modified
 * @property Carbon        post_modified_gmt
 * @property User          author
 * @property Comment[]     comments
 * @property PostMeta[]    meta
 * @property ThumbnailMeta thumbnail
 * @property Attachment    attachment
 */
class Post extends Model
{

    use WithMeta, Aliases;

    /**
     * @var string
     */
    const CREATED_AT = 'post_date';

    /**
     * @var string
     */
    const UPDATED_AT = 'post_modified';
    /**
     * @var array
     */
    protected static $aliases = [
        'title'      => 'post_title',
        'content'    => 'post_content',
        'excerpt'    => 'post_excerpt',
        'slug'       => 'post_name',
        'type'       => 'post_type',
        'mime_type'  => 'post_mime_type',
        'url'        => 'guid',
        'author_id'  => 'post_author',
        'parent_id'  => 'post_parent',
        'created_at' => 'post_date',
        'updated_at' => 'post_modified',
        'status'     => 'post_status',
    ];
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
     * @var string
     */
    protected $postType = 'post';
    /**
     * @var array
     */
    protected $fillable = [
        'post_content',
        'post_title',
        'post_excerpt',
        'post_type',
        'to_ping',
        'pinged',
        'post_content_filtered',
    ];

    /**
     * Filter by post type
     *
     * @param        $query
     * @param string $type
     *
     * @return mixed
     */
    public function scopeType( $query, $type = 'post' )
    {
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
    public function scopeStatus( $query, $status = 'publish' )
    {
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
    public function scopeAuthor( $query, $author = null )
    {
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
    public function comments()
    {
        return $this->hasMany( Comment::class, 'comment_post_ID' );
    }

    /**
     * @param string $taxonomy
     *
     * @return TermTaxonomy[] | Collection
     */
    public function taxonomy( string $taxonomy )
    {
        return $this->taxonomies()->where( 'taxonomy', '=', $taxonomy )->get();
    }

    /**
     * @return BelongsToMany
     */
    public function taxonomies()
    {
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
    public function addTerms( string $taxonomy, array $terms )
    {
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
     * @return HasMany
     */
    public function attachment()
    {
        return $this->children()->where( 'post_type', 'attachment' );
    }

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany( static::class, 'post_parent' );
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    public function save( array $options = [] )
    {
        $preWpPost = new WP_Post( (object) $this->original );

        $didExist = $this->exists;

        if ( $didExist ) {
            do_action( 'pre_post_update', $this->ID, $preWpPost );
        }

        $result = parent::save( $options );
        $wpPost = $this->toWpPost();

        if ( $result && $didExist ) {
            do_action( 'edit_post', $this->ID, $wpPost );
            do_action( 'post_updated', $this->ID, $wpPost, $preWpPost );
        }

        do_action( 'save_post', $this->ID, $wpPost, $didExist );
        do_action( 'wp_insert_post', $this->ID, $wpPost, $didExist );

        clean_post_cache( $this->ID );

        return $result;
    }

    /**
     * @return WP_Post
     */
    public function toWpPost(): WP_Post
    {
        return new WP_Post( (object) $this->toArray() );
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo( User::class, 'post_author' );
    }

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo( static::class, 'post_parent' );
    }

    /**
     * Returns posts thumbnail
     *
     * @return HasOne
     */
    public function thumbnail()
    {
        return $this->hasOne( ThumbnailMeta::class, 'post_id' )
                    ->where( 'meta_key', '_thumbnail_id' );
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return PostBuilder
     */
    public function newEloquentBuilder( $query )
    {
        $builder = new PostBuilder( $query );

        if ( $this->postType ) {
            return $builder->where( 'post_type', '=', $this->postType );
        }

        return $builder;
    }

    /**
     * @return Builder
     */
    public function newQuery()
    {
        return $this->postType ?
            parent::newQuery()->where( 'post_type', '=', $this->postType ) :
            parent::newQuery();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->posts;
    }

    /**
     * This method acts as pure annotation for IDEs
     *
     * @return PostBuilder
     */
    public static function query()
    {
        /**
         * @var PostBuilder $query
         */
        $query = parent::query();

        return $query;
    }

}
