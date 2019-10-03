<?php
namespace Mossengine\Poignant\Errors\Reasons;

use Mossengine\Poignant\Extensions\Container;

/**
 * Class ReasonsContainer
 * @package Mossengine\Poignant\Errors\Reasons
 */
class ReasonsContainer extends Container
{
    /**
     * @var string
     */
    protected $classItem = Reason::class;

    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->map(function($item) {
            return $item();
        });
    }

    /**
     * @param \Closure $closure
     * @return array
     */
    public function map(\Closure $closure)
    {
        echo PHP_EOL . json_encode('reasons = ' . count($this->arrayContainer)) . PHP_EOL;
        echo PHP_EOL . json_encode($this->arrayContainer) . PHP_EOL;

        return parent::map($closure);
    }
}