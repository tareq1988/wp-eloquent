<?php

namespace UnderScorer\ORM\Tests\Factories;

use UnderScorer\ORM\Eloquent\Model;
use WP_UnitTest_Factory_For_Thing;

/**
 * Class BaseFactory
 * @package UnderScorer\ORM\Tests\Factories
 */
abstract class BaseFactory implements FactoryInterface
{

    /**
     * @var string|Model
     */
    protected $modelClass;

    /**
     * @var WP_UnitTest_Factory_For_Thing
     */
    private $WPFactory;

    /**
     * BaseFactory constructor.
     *
     * @param WP_UnitTest_Factory_For_Thing $factory
     */
    public function __construct( WP_UnitTest_Factory_For_Thing $factory )
    {
        $this->WPFactory = $factory;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create( array $attributes = [] ): Model
    {

        $itemID = $this->WPFactory->create( $attributes );

        /**
         * @var Model $model
         */
        $model = $this->modelClass::query()->find( $itemID );

        return $model;

    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @param string $modelClass
     *
     * @return $this
     */
    public function setModelClass( string $modelClass )
    {
        $this->modelClass = $modelClass;

        return $this;
    }

}
