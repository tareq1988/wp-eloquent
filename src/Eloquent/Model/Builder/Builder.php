<?php

namespace AmphiBee\Eloquent\Model\Builder;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;

/**
 * Class Builder
 *
 * @author Thomas Georgel <thomas@hydrat.agency>
 * @author AmphiBee <hello@amphibee.fr>
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class Builder extends BaseBuilder
{
    /**
     * Fix PostgreSQL format causing error on mysql.
     *
     * TODO: read configuration to determine the Database engine /!\
     *
     * @param  string  $seed
     * @return PostBuilder
     */
    public function inRandomOrder($seed = '')
    {
        return $this->orderByRaw('RAND()');
    }
}