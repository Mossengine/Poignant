<?php
namespace Mossengine\Poignant\Validators;

use Mossengine\Poignant\Extensions\Container;

/**
 * Class ValidatorsContainer
 * @package Mossengine\Poignant\Validators
 */
class ValidatorsContainer extends Container
{
    /**
     * @var string
     */
    protected $classItem = Validator::class;
}