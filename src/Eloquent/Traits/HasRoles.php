<?php

namespace AmphiBee\Eloquent\Traits;

/**
 * Trait HasRoles
 *
 * @package AmphiBee\Eloquent\Traits
 */
trait HasRoles
{

    /**
     * @param string $role
     * @return boolean
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->capabilities);
    }

    /**
     * @param array $roles
     * @return boolean
     */
    public function hasAnyRoles($roles = []): bool
    {
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getCapabilitiesAttribute(): array
    {
        return array_keys($this->getMeta('wp_capabilities'));
    }

    /**
     * @return boolean
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('administrator');
    }
}
