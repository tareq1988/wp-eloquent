<?php

namespace AmphiBee\Eloquent\Traits;

use AmphiBee\Eloquent\Core\Helpers;

/**
 * Trait HasMeta
 *
 * @package AmphiBee\Eloquent\Traits
 */
trait HasMeta
{

    /**
     * Retrieves a meta filed.
     *
     * @param null|string $meta_key
     * @return void
     */
    public function getMeta(?string $meta_key)
    {
        $meta_value = '';

        if ($meta_key) {
            $meta_value = $this->meta()->where('meta_key', $meta_key)->pluck('meta_value')->first();

            if (Helpers::isSerialized($meta_value)) {
                $meta_value = unserialize($meta_value);
            }
        }

        return $meta_value;
    }

    /**
     * Updates a meta field.
     *
     * @param string $meta_key
     * @param mixed $value
     * @return self
     */
    public function setMeta(string $meta_key, $value): self
    {
        $value = is_array($value) ? serialize($value) : $value;
        $meta  = $this->meta()->firstOrCreate(['meta_key' => $meta_key]);
        $meta  = $this->meta()->where(['meta_key' => $meta_key])->update(['meta_value' => $value]);

        return $this;
    }

    /**
     * Deletes all meta for this object with given key
     *
     * @param string $meta_key
     * @return void
     */
    public function deleteMeta(string $meta_key)
    {
        $this->meta()->where('meta_key', $meta_key)->delete();
    }
}
