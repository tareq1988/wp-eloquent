<?php

namespace AmphiBee\Eloquent\Model\Builder;

use AmphiBee\Eloquent\Connection;

/**
 * Class TermBuilder
 *
 * @package AmphiBee\Eloquent
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class TermBuilder extends Builder
{
    /**
     * Get terms where taxonomy name is/in $names
     *
     * @param string|string[]  $names
     *
     * @return TermBuilder
     */
    public function whereTaxonomy($names)
    {
        return $this->whereHas('taxonomy', function ($q) use ($names) {
            $q->whereIn('taxonomy', is_array($names) ? $names : [$names]);
        });
    }


    /**
     * Order the results using a custom meta key.
     *
     * eg. : $query->orderByMeta('meta_key', 'DESC')
     *
     * @param string   $meta_key
     * @param string   $order
     *
     * @return TermBuilder
     */
    public function orderByMeta(string $meta_key, string $order = 'ASC')
    {
        $db     = Connection::instance();
        $prefix = $db->getPdo()->prefix();

        return $this->select([$prefix.'terms.*', $db->raw("(select meta_value from {$prefix}termmeta where {$prefix}termmeta.meta_key = '{$meta_key}' and {$prefix}terms.term_id = {$prefix}termmeta.term_id limit 1) as meta_ordering")])
                ->orderByRaw('LENGTH(meta_ordering)', 'ASC') # alphanum support, avoid this kind of sort : 1, 10, 11, 7, 8
                ->orderBy('meta_ordering', $order);
    }
}
