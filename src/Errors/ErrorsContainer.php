<?php
namespace Mossengine\Poignant\Errors;

use Mossengine\Poignant\Extensions\Container;

/**
 * Class ErrorsContainer
 * @package Mossengine\Poignant\Errors
 */
class ErrorsContainer extends Container
{
    /**
     * @var string
     */
    protected $classItem = Error::class;

    /**
     * @param \Closure $closure
     * @return array
     */
    public function map(\Closure $closure)
    {
        echo PHP_EOL . json_encode('errors = ' . count($this->arrayContainer)) . PHP_EOL;
        echo PHP_EOL . json_encode($this->arrayContainer) . PHP_EOL;

        return parent::map($closure);
    }
}