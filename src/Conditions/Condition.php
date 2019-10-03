<?php
namespace Mossengine\Poignant\Conditions;

use Mossengine\Poignant\Conditions\Rules\RulesContainer;
use Mossengine\Poignant\Extensions\Item;

/**
 * Class Condition
 * @package Mossengine\Poignant\Conditions
 */
class Condition extends Item
{
    /**
     * Condition constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Call the parent to satisfy OOP
        parent::__construct($arrayParameters);

        // Setup default validators
        $this->rules(new RulesContainer(
            array_get($arrayParameters, 'rules', [])
        ));
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'rules' => $this->rules()()
        ];
    }

    /**
     * @param $stringMethod
     * @param $arrayArguments
     * @return $this
     */
    public function __call($stringMethod, $arrayArguments) {
        // Add the rule to the rules
        $this->rules()->set(
            implode(
                '|',
                array_merge(
                    [
                        ($boolReverse = ('not' === array_get(preg_split('/(?=[A-Z])/', $stringMethod), 0, null)) ? '!' : '')
                        . strtolower($boolReverse ? substr($stringMethod, 3) : $stringMethod)
                    ],
                    array_filter(
                        array_map(
                            function($mixedArgument) {
                                return (
                                    is_string($mixedArgument)
                                    || is_numeric($mixedArgument)
                                        ? $mixedArgument
                                        : (
                                            is_array($mixedArgument)
                                                ? implode(
                                                    ',',
                                                    array_filter(
                                                        array_map(
                                                            function($mixedArgumentItem) {
                                                                return (
                                                                    is_string($mixedArgumentItem)
                                                                    || is_numeric($mixedArgumentItem)
                                                                        ? $mixedArgumentItem
                                                                        : null
                                                                );
                                                            },
                                                            $mixedArgument
                                                        )
                                                    )
                                                )
                                                : null
                                        )
                                );
                            },
                            $arrayArguments
                        )
                    )
                )
            )
        );

        // Return this for chaining
        return $this;
    }

    /**
     * @param null $containerRules
     * @return $this|mixed
     */
    public function rules($containerRules = null)
    {
        if (
            !is_null($containerRules)
            && ($containerRules instanceof RulesContainer)
        ) {
            $this->attributeSet('rules', $containerRules);
            return$this;
        }
        return $this->attributeGet('rules', new RulesContainer());
    }
}