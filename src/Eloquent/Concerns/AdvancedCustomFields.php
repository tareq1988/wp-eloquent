<?php

namespace AmphiBee\Eloquent\Concerns;

use AmphiBee\Eloquent\Plugins\Acf\AdvancedCustomFields as BaseAdvancedCustomFields;

/**
 * Trait HasAcfFields
 *
 * @package AmphiBee\Eloquent\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait AdvancedCustomFields
{
    /**
     * @return AdvancedCustomFields
     */
    public function getAcfAttribute()
    {
        return new BaseAdvancedCustomFields($this);
    }
}
