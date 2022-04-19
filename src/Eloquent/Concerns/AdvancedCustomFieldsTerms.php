<?php

namespace AmphiBee\Eloquent\Concerns;

use AmphiBee\Eloquent\Plugins\Acf\AdvancedCustomFields as BaseAdvancedCustomFields;

/**
 * Trait HasAcfFields
 *
 * @package OP\Lib\WpEloquent\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait AdvancedCustomFieldsTerms
{
    /**
     * @return AdvancedCustomFields
     */
    public function getAcfAttribute()
    {
        return new BaseAdvancedCustomFields($this);
    }

    /**
     * Get Post's ACF fields
     *
     * @param bool $format_value weither to apply acf formating or not
     * @return mixed
     *
     * @reference https://www.advancedcustomfields.com/resources/get_fields/
     * @since 1.0.0
     */
    public function getFields(bool $format_value = true)
    {
        return get_fields($this->getAcfFromIdentifier(), $format_value);
    }


    /**
     * Get an ACF field from a given key
     *
     * @param string $key Selector
     * @param bool $format_value
     * @return mixed
     *
     * @reference https://www.advancedcustomfields.com/resources/get_field/
     * @since 1.0.0
     */
    public function getField(string $key, bool $format_value = true)
    {
        return get_field($key, $this->getAcfFromIdentifier(), $format_value);
    }


    /**
     * Set/Update an ACF field from a given key and value
     *
     * @param string $key   The field name or field key.
     * @param mixed  $value The new value.
     *
     * @reference https://www.advancedcustomfields.com/resources/update_field/
     * @since 1.0.1
     */
    public function setField(string $key, $value)
    {
        return update_field($key, $value, $this->getAcfFromIdentifier());
    }
    
    
    /**
     * Set/Update ACF fields from a given key => value pair array
     *
     * @param string $fields The field array containing keys => values
     *
     * @return $this
     * @reference https://www.advancedcustomfields.com/resources/update_field/
     * @since 1.0.4
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $key => $value) {
            update_field($key, $value, $this->getAcfFromIdentifier());
        }
        return $this;
    }


    /**
     * Get the ACF identifier for terms.
     * Used bu ACF get_field() method.
     *
     * @return string
     */
    public function getAcfFromIdentifier(): string
    {
        return sprintf('%s_%s', 'term', $this->term_id);
    }
}
