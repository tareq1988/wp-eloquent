<?php

namespace UnderScorer\ORM\Contracts;

/**
 * Interface MetaInterface
 * @package UnderScorer\ORM\Contracts
 */
interface MetaInterface
{

    /**
     * @return mixed
     */
    public function getMetaValue();

    /**
     * @param $value
     *
     * @return static
     */
    public function setMetaValue( $value );

    /**
     * @return string
     */
    public function getMetaKey(): string;

    /**
     * @param string $key
     *
     * @return static
     */
    public function setMetaKey( string $key );

}
