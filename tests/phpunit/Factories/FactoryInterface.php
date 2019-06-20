<?php

namespace UnderScorer\ORM\Tests\Factories;

use UnderScorer\ORM\Eloquent\Model;

/**
 * Interface FactoryInterface
 * @package UnderScorer\ORM\Tests\Factories
 */
interface FactoryInterface
{

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create( array $attributes = [] ): Model;

    /**
     * @return string
     */
    public function getModelClass(): string;

    /**
     * @param string $modelClass
     *
     * @return static
     */
    public function setModelClass( string $modelClass );

}
