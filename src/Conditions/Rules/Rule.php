<?php
namespace Mossengine\Poignant\Conditions\Rules;

use Mossengine\Poignant\Extensions\Item;

/**
 * Class Rule
 * @package Mossengine\Poignant\Conditions\Rules
 */
class Rule extends Item
{
    /**
     * Rule constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Call the parent to satisfy OOP
        parent::__construct($arrayParameters);

        // Key ( raw validator settings )
        $stringRuleRaw = $this->key();

        // Reverse
        $this->reverse('!' === substr($stringRuleRaw, 0, 1));

        // Parameters
        $this->parameters(explode('|', substr($stringRuleRaw, ($this->reverse() ? 1 : 0))));

        // Validator
        $this->validator($this->parameterGet(0));
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->key();
    }

    /**
     * @param null $boolReverse
     * @return $this|mixed
     */
    public function reverse($boolReverse = null)
    {
        if (
            !is_null($boolReverse)
            && is_bool($boolReverse)
        ) {
            $this->attributeSet('reverse', $boolReverse);
            return $this;
        }
        return $this->attributeGet('reverse');
    }

    /**
     * @param null $stringValidator
     * @return $this|mixed
     */
    public function validator($stringValidator = null)
    {
        if (
            !is_null($stringValidator)
            && is_string($stringValidator)
        ) {
            $this->attributeSet('validator', $stringValidator);
            return $this;
        }
        return $this->attributeGet('validator');
    }

    /**
     * @param null $arrayParameters
     * @return $this|array|Item
     */
    public function parameters($arrayParameters = null) {
        if (
            !is_null($arrayParameters)
            && is_array($arrayParameters)
        ) {
            $this->attributeSet('parameters', $arrayParameters);
            return $this;
        }
        return $this->attributeGet('parameters');
    }

    /**
     * @param $stringKey
     * @param null $mixedDefault
     * @return mixed
     */
    public function parameterGet($stringKey, $mixedDefault = null) {
        return $this->attributeGet('parameters.' . $stringKey, $mixedDefault);
    }

    /**
     * @param $stringKey
     * @param $mixedValue
     * @return $this
     */
    public function parameterSet($stringKey, $mixedValue) {
        $this->attributeSet('parameters.' . $stringKey, $mixedValue);

        return $this;
    }
}