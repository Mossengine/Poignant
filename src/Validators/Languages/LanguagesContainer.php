<?php
namespace Mossengine\Poignant\Validators\Languages;

use Mossengine\Poignant\Extensions\Container;

/**
 * Class LanguagesContainer
 * @package Mossengine\Poignant\Validators\Languages
 */
class LanguagesContainer extends Container
{
    /**
     * @var string
     */
    protected $classItem = Language::class;
}