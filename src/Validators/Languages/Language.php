<?php
namespace Mossengine\Poignant\Validators\Languages;

use Mossengine\Poignant\Extensions\Item;

/**
 * Class Language
 * @package Mossengine\Poignant\Validators\Languages
 */
class Language extends Item
{
    /**
     * Rule constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Call the parent to satisfy OOP
        parent::__construct($arrayParameters);

        // Normal
        $this->normal(array_get($arrayParameters, 'normal', 'No reason for normal condition'));

        // Reverse
        $this->reverse(array_get($arrayParameters, 'reverse', 'No reason for reverse condition'));
    }

    /**
     * @param null $stringNormal
     * @return string|null
     *
     * This function will return the normal translation for this language
     */
    public function normal($stringNormal = null) {
        if (
            !is_null($stringNormal)
            && is_string($stringNormal)
        ) {
            $this->attributeSet('normal', $stringNormal);
            return$this;
        }
        return $this->attributeGet('normal');
    }

    /**
     * @param null $stringReverse
     * @return string|null
     *
     * This function will return the reverse translation for this language*
     */
    public function reverse($stringReverse = null) {
        if (
            !is_null($stringReverse)
            && is_string($stringReverse)
        ) {
            $this->attributeSet('reverse', $stringReverse);
            return$this;
        }
        return $this->attributeGet('reverse');
    }
}