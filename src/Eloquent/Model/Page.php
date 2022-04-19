<?php

namespace AmphiBee\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use AmphiBee\Eloquent\Model\Contract\WpEloquentPost;

/**
 * Class Page
 *
 * @package AmphiBee\Eloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class Page extends Post implements WpEloquentPost
{
    /**
     * @var string
     */
    protected $postType = 'page';

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeHome(Builder $query)
    {
        return $query
            ->where('ID', '=', Option::get('page_on_front'))
            ->limit(1);
    }

    /**
     * Filter page which has template. If $template is provided, check filter pages with this template.
     *
     * @param Builder $query
     * @param string|array $template
     * @param string $operator (=, !=, in, not in..)
     * @return Builder
     */
    public function scopeHasTemplate(Builder $query, $template = null, string $operator = '=')
    {
        # Compare with asked template value
        if ($template !== null) {
            return $this->scopeHasMeta($query, '_wp_page_template', $template, $operator);
        }
        
        # No template asked, Looking for pages with templates which are not 'default'
        return $this->scopeHasMeta($query, '_wp_page_template')
                    ->scopeHasMeta($query, '_wp_page_template', 'default', '!=');
    }
    
    
    /**
     * Get the page template.
     *
     * @return string
     */
    public function getTemplateAttribute(): string
    {
        return $this->meta->_wp_page_template ?: '';
        ;
    }
}
