<?php
namespace Mossengine\Poignant\Conditions\Rules;

use Mossengine\Poignant\Extensions\Container;

/**
 * Class RulesContainer
 * @package Mossengine\Poignant\Conditions\Rules
 */
class RulesContainer extends Container
{
    /**
     * @var string
     */
    protected $classItem = Rule::class;

    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->map(function($item) {
            return $item();
        });
    }
}