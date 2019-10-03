<?php
namespace Mossengine\Poignant;

use Mossengine\Poignant\Conditions\ConditionsContainer;
use Mossengine\Poignant\Conditions\Condition;
use Mossengine\Poignant\Conditions\Rules\Rule;
use Mossengine\Poignant\Errors\Error;
use Mossengine\Poignant\Errors\ErrorsContainer;
use Mossengine\Poignant\Errors\Reasons\Reason;
use Mossengine\Poignant\Errors\Reasons\ReasonsContainer;
use Mossengine\Poignant\Extensions\Container;
use Mossengine\Poignant\Helpers\Templator;
use Mossengine\Poignant\Validators\Languages\Language;
use Mossengine\Poignant\Validators\Validator;
use Mossengine\Poignant\Validators\ValidatorsContainer;
use Ramsey\Uuid\Uuid;

/**
 * Class Poignant2
 * @package Mossengine\Poignant
 */
class Poignant2
{
    /**
     * Poignant2 constructor.
     * @param array $arrayParameters
     */
    public function __construct(array $arrayParameters = [])
    {
        try {
            $stringUuidV4 = Uuid::uuid4()->toString();
        } catch (\Exception $e) {
            $stringUuidV4 = md5(rand(1,999) . '_' . time() . '_' . '!isset' . '_' . rand(1,999));
        }

        $this->notSet(
            $stringUuidV4
        )
            ->validators(
                new ValidatorsContainer(
                    array_merge(
                        $this->constructDefaultValidators(),
                        is_array($mixedValidators = array_get($arrayParameters, 'validators', []))
                            ? $mixedValidators
                            : (
                                $mixedValidators instanceof Container
                                    ? $mixedValidators()
                                    : []
                        )
                    )
                )
            )
            ->conditions(
                new ConditionsContainer(
                    is_array($mixedConditions = array_get($arrayParameters, 'conditions', []))
                        ? $mixedConditions
                        : (
                            $mixedConditions instanceof Container
                                ? $mixedConditions()
                                : []
                    )
                )
            )
            ->errors(
                new ErrorsContainer(
                    is_array($mixedErrors = array_get($arrayParameters, 'errors', []))
                        ? $mixedErrors
                        : (
                            $mixedErrors instanceof Container
                                ? $mixedErrors()
                                : []
                    )
                )
            )
            ->data(
                array_get($arrayParameters, 'data', [])
            )
            ->language(
                array_get($arrayParameters, 'language', 'en')
            )
            ->languages(
                array_get($arrayParameters, 'languages', [])
            );
    }

    /**
     * @param $stringFunction
     * @param $arrayArguments
     * @return mixed
     */
    public static function __callStatic($stringFunction, $arrayArguments)
    {
        $poignant = new Poignant2((is_array($arrayParameters = array_get($arrayArguments, 0, null)) ? $arrayParameters : []));
        return call_user_func_array([$poignant, $stringFunction], $arrayArguments);
    }

    /**
     * @param $stringMethod
     * @param $arrayArguments
     * @return $this
     */
    public function __call($stringMethod, $arrayArguments) {
        // Check for 'with' prefix
        if ('with' === strtolower(substr($stringMethod, 0, 4))) {
            // Get and Check for condition key length > 0 else no key to work with...
            if (strlen($stringConditionKey = substr($stringMethod, 4)) > 0) {
                // Define a new condition
                $condition = new Condition([
                    'key' => (
                        $stringConditionKey = strtolower(implode('.', preg_split('/(?=[A-Z])/', $stringConditionKey, -1, PREG_SPLIT_NO_EMPTY)))
                    )
                ]);

                // add a new condition to the conditions and return the condition for allowing further chaining to occur
                $this->conditions()->set($stringConditionKey, $condition);

                if (
                    ($closure = array_get($arrayArguments, 0)) instanceof \Closure
                ) {
                    call_user_func($closure, $condition);
                }
            }
        }

        // Return this as there was no magic functions...
        return $this;
    }

    /**
     * @param $stringAttribute
     * @return |null
     */
    public function __get($stringAttribute)
    {
        if ('data' === $stringAttribute) {
            return $this->data();
        }

        if (
            method_exists($this, $stringAttribute)
            && $this->{$stringAttribute}() instanceof Container
            && method_exists($this->{$stringAttribute}(), '__invoke')
        ) {
            return $this->{$stringAttribute}()();
        }

        return null;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'validators' => $this->validators,
            'conditions' => $this->conditions,
            'errors' => $this->errors,
            'data' => $this->data,
        ];
    }

    /**
     * @param array $arrayParameters
     * @return Poignant2
     */
    public static function create(array $arrayParameters = [])
    {
        return new Poignant2($arrayParameters);
    }

    /**
     * @var string
     */
    private $stringNotSet;

    /**
     * @param null $stringNotSet
     * @return $this|string
     */
    public function notSet($stringNotSet = null)
    {
        if (
            !is_null($stringNotSet)
        ) {
            $this->stringNotSet = $stringNotSet;
            return $this;
        }
        return $this->stringNotSet;
    }

    /**
     * @var string
     */
    private $stringLanguage = 'en';

    /**
     * @param null $stringLanguage
     * @return $this|string
     */
    public function language($stringLanguage = null)
    {
        if (
            !is_null($stringLanguage)
        ) {
            $this->stringLanguage = $stringLanguage;
            return $this;
        }
        return $this->stringLanguage;
    }

    /**
     * @param array $arrayValidatorLanguages
     * @return $this
     */
    public function languages($arrayValidatorLanguages = []) {
        if (
            is_array($arrayValidatorLanguages)
            && !empty($arrayValidatorLanguages)
        ) {
            foreach ($arrayValidatorLanguages as $stringValidator => $arrayLanguages) {
                if (
                    (
                        $validator = $this->validators()->get($stringValidator)
                    ) instanceof Validator
                ) {
                    foreach ($arrayLanguages as $stringLanguageKey => $arrayLanguageReasons) {
                        // Updating existing language or creating new
                        if (
                            (
                                $language = $validator->languages()->get($stringLanguageKey)
                            ) instanceof Language
                        ) {
                            // Update
                            $language->normal(array_get($arrayLanguageReasons, 'normal', $language->normal()))
                                ->reverse(array_get($arrayLanguageReasons, 'reverse', $language->reverse()));
                        } else {
                            // New
                            $validator->languages()->set(
                                $stringLanguageKey,
                                new Language([
                                    'key' => $stringLanguageKey,
                                    'normal' => array_get($arrayLanguageReasons, 'normal', null),
                                    'reverse' => array_get($arrayLanguageReasons, 'reverse', null)
                                ])
                            );
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @var
     *
     * This is the container for Validators
     */
    private $containerValidators;

    /**
     * @param null $containerValidators
     * @return $this|ValidatorsContainer
     */
    public function validators($containerValidators = null)
    {
        if (
            $containerValidators instanceof ValidatorsContainer
        ) {
            $this->containerValidators = $containerValidators;
            return $this;
        }
        return $this->containerValidators = (
            $this->containerValidators instanceof ValidatorsContainer
                ? $this->containerValidators
                : new ValidatorsContainer()
        );
    }

    /**
     * @var
     *
     * This is the container for Conditions
     */
    private $containerConditions;

    /**
     * @param null $containerConditions
     * @return $this|ConditionsContainer
     */
    public function conditions($containerConditions = null)
    {
        if (
            $containerConditions instanceof ConditionsContainer
        ) {
            $this->containerConditions = $containerConditions;
            return $this;
        }
        return $this->containerConditions = (
            $this->containerConditions instanceof ConditionsContainer
                ? $this->containerConditions
                : new ConditionsContainer()
        );
    }

    /**
     * @var
     *
     * This is the container for Errors
     */
    private $containerErrors;

    /**
     * @param null $containerErrors
     * @return $this|ErrorsContainer
     */
    public function errors($containerErrors = null)
    {
        if (
            $containerErrors instanceof ErrorsContainer
        ) {
            $this->containerErrors = $containerErrors;
            return $this;
        }
        return $this->containerErrors = (
            $this->containerErrors instanceof ErrorsContainer
                ? $this->containerErrors
                : new ErrorsContainer()
        );
    }

    /**
     * @return array
     *
     * This function provides an array of all the default Validators
     */
    private function constructDefaultValidators()
    {
        return [
            'required' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        $this->notSet()
                        !== array_get(
                            array_get($arrayParameters, 'data', []),
                            array_get($arrayParameters, 'key', $this->notSet()),
                            $this->notSet()
                        )
                        && !empty(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] is required',
                        'reverse' => '[{{key}}={{value}}] is not required'
                    ]
                ]
            ],
            'isset' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        $this->notSet()
                        !== array_get(
                            array_get($arrayParameters, 'data', []),
                            array_get($arrayParameters, 'key', $this->notSet()),
                            $this->notSet()
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be set',
                        'reverse' => '[{{key}}={{value}}] must not be set'
                    ]
                ]
            ],
            'empty' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        empty(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be empty',
                        'reverse' => '[{{key}}={{value}}] must not be empty'
                    ]
                ]
            ],
            'uuid' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        Uuid::isValid(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be valid uuid',
                        'reverse' => '[{{key}}={{value}}] must not be valid uuid'
                    ]
                ]
            ],
            'email' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        false !== filter_var(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            ),
                            FILTER_VALIDATE_EMAIL
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'string' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_string(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'array' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_array(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'object' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_object(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'numeric' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_numeric(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid number',
                        'reverse' => '[{{key}}={{value}}] must not be a valid number'
                    ]
                ]
            ],
            'integer' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_integer(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'float' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_float(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'carbon' => [
                'closure' => function($arrayParameters = []) {
                    try {
                        return (
                            (
                                $mixedDatetime = array_get(
                                    ($arrayData = array_get($arrayParameters, 'data', [])),
                                    ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                    'invalid datetime string'
                                )
                            )
                            === \Carbon\Carbon::createFromFormat(
                                ($stringFormat = array_get($arrayParameters, 'parameters.1', '')),
                                $mixedDatetime
                            )->format($stringFormat)
                        );
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must match the following format [{{parameters.1}}]',
                        'reverse' => '[{{key}}={{value}}] must not match the following format [{{parameters.1}}]'
                    ]
                ]
            ],
            'datetime' => [
                'closure' => function($arrayParameters = []) {
                    try {
                        return (
                            (
                                $mixedDatetime = array_get(
                                    ($arrayData = array_get($arrayParameters, 'data', [])),
                                    ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                    'invalid datetime string'
                                )
                            )
                            === \Carbon\Carbon::parse($mixedDatetime)->toDateTimeString()
                        );
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid date and time [Y-m-d H:i:s]',
                        'reverse' => '[{{key}}={{value}}] must not be a valid date and time [Y-m-d H:i:s]'
                    ]
                ]
            ],
            'date' => [
                'closure' => function($arrayParameters = []) {
                    try {
                        return (
                            (
                                $mixedDatetime = array_get(
                                    ($arrayData = array_get($arrayParameters, 'data', [])),
                                    ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                    'invalid date string'
                                )
                            )
                            === \Carbon\Carbon::parse($mixedDatetime)->toDateString()
                        );
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid date [Y-m-d]',
                        'reverse' => '[{{key}}={{value}}] must not be a valid date [Y-m-d]'
                    ]
                ]
            ],
            'time' => [
                'closure' => function($arrayParameters = []) {
                    try {
                        return (
                            (
                                $mixedDatetime = array_get(
                                    ($arrayData = array_get($arrayParameters, 'data', [])),
                                    ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                    'invalid time string'
                                )
                            )
                            === \Carbon\Carbon::parse($mixedDatetime)->toTimeString()
                        );
                    } catch (\Exception $e) {
                        return false;
                    }
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid time [H:i:s]',
                        'reverse' => '[{{key}}={{value}}] must not be a valid time [H:i:s]'
                    ]
                ]
            ],
            'boolean' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        is_bool(
                            $mixedValue = array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet()))
                            )
                        ) || (
                            is_string($mixedValue)
                            && in_array(
                                strtolower($mixedValue),
                                ['0', '1', 'yes', 'no', 'true', 'false']
                            )
                        )
                        || (
                            is_integer($mixedValue)
                            && in_array(
                                $mixedValue,
                                [0, 1]
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a valid {{validator}}',
                        'reverse' => '[{{key}}={{value}}] must not be a valid {{validator}}'
                    ]
                ]
            ],
            'true' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        true === filter_var(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                false
                            ),
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a boolean with the value of true',
                        'reverse' => '[{{key}}={{value}}] must not be a boolean with the value of true'
                    ]
                ]
            ],
            'false' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        false === filter_var(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                true
                            ),
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be a boolean with the value of false',
                        'reverse' => '[{{key}}={{value}}] must not be a boolean with the value of false'
                    ]
                ]
            ],
            'in' => [
                'closure' => function($arrayParameters = []) {
                    return (
                        in_array(
                            array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                $this->notSet()
                            ),
                            is_array($arrayComparison = array_get($arrayParameters, 'parameters.1', []))
                                ? $arrayComparison
                                : (
                                    is_array($arrayComparison = explode(',', $arrayComparison))
                                        ? $arrayComparison
                                        : []
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be in [{{parameters.1}}]',
                        'reverse' => '[{{key}}={{value}}] must not be in [{{parameters.1}}]'
                    ]
                ]
            ],
            'length' => [
                'closure' => function($arrayParameters = []) {
                    // Define a null length
                    $intLength = null;

                    // Define the operator
                    $stringOperator = array_get($arrayParameters, 'parameters.1', null);

                    // Define the comparison
                    $intComparison = intval(array_get($arrayParameters, 'parameters.2', null));

                    // String?
                    if (
                        is_string(
                            $mixedValue = array_get(
                                ($arrayData = array_get($arrayParameters, 'data', [])),
                                ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                                null
                            )
                        )
                    ) {
                        $intLength = strlen($mixedValue);
                    } else if (
                        is_array($mixedValue)
                        || is_object($mixedValue)
                    ) {
                        $intLength = count((array)$mixedValue);
                    }

                    return (
                        !is_null($intLength)
                        && !is_null($stringOperator)
                        && (
                            (
                                (
                                    'lt' === $stringOperator
                                    || '<' === $stringOperator
                                )
                                && $intLength < $intComparison
                            )
                            || (
                                (
                                    'lte' === $stringOperator
                                    || 'elt' === $stringOperator
                                    || '<=' === $stringOperator
                                    || '=<' === $stringOperator
                                )
                                && $intLength <= $intComparison
                            )
                            || (
                                (
                                    'eq' === $stringOperator
                                    || '=' === $stringOperator
                                    || '==' === $stringOperator
                                    || '===' === $stringOperator
                                )
                                && $intLength === $intComparison
                            )
                            || (
                                (
                                    'gte' === $stringOperator
                                    || 'egt' === $stringOperator
                                    || '>=' === $stringOperator
                                    || '=>' === $stringOperator
                                )
                                && $intLength >= $intComparison
                            )
                            || (
                                (
                                    'gt' === $stringOperator
                                    || '>' === $stringOperator
                                )
                                && $intLength > $intComparison
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be {{parameter.1}} {{parameters.2}}',
                        'reverse' => '[{{key}}={{value}}] must not be {{parameter.1}} {{parameters.2}}'
                    ]
                ]
            ],
            'compare' => [
                'closure' => function($arrayParameters = []) {
                    // Define the operator
                    $stringOperator = array_get($arrayParameters, 'parameters.1', null);

                    // Define the comparison
                    $intComparison = array_get($arrayParameters, 'parameters.2', null);

                    // Define the mixed value
                    $mixedValue = array_get(
                        ($arrayData = array_get($arrayParameters, 'data', [])),
                        ($stringRuleKey = array_get($arrayParameters, 'key', $this->notSet())),
                        null
                    );

                    // Parsing ?
                    switch (array_get($arrayParameters, 'parameters.3', null)) {
                        case 'integer':
                            $intComparison = intval($intComparison);
                            $mixedValue = intval($mixedValue);
                            break;
                        case 'float':
                            $intComparison = floatval($intComparison);
                            $mixedValue = floatval($mixedValue);
                            break;
                    }

                    return (
                        !is_null($stringOperator)
                        && (
                            (
                                (
                                    'lt' === $stringOperator
                                    || '<' === $stringOperator
                                )
                                && $mixedValue < $intComparison
                            )
                            || (
                                (
                                    'lte' === $stringOperator
                                    || 'elt' === $stringOperator
                                    || '<=' === $stringOperator
                                    || '=<' === $stringOperator
                                )
                                && $mixedValue <= $intComparison
                            )
                            || (
                                (
                                    'eq' === $stringOperator
                                    || '=' === $stringOperator
                                    || '==' === $stringOperator
                                )
                                && $mixedValue == $intComparison
                            )
                            || (
                                (
                                    'identical' === $stringOperator
                                    || 'same' === $stringOperator
                                    || '===' === $stringOperator
                                )
                                && $mixedValue === $intComparison
                            )
                            || (
                                (
                                    'gte' === $stringOperator
                                    || 'egt' === $stringOperator
                                    || '>=' === $stringOperator
                                    || '=>' === $stringOperator
                                )
                                && $mixedValue >= $intComparison
                            )
                            || (
                                (
                                    'gt' === $stringOperator
                                    || '>' === $stringOperator
                                )
                                && $mixedValue > $intComparison
                            )
                        )
                    );
                },
                'languages' => [
                    'en' => [
                        'normal' => '[{{key}}={{value}}] must be {{parameters.1}} {{parameters.2}}',
                        'reverse' => '[{{key}}={{value}}] must not be {{parameters.1}} {{parameters.2}}'
                    ]
                ]
            ],
        ];
    }

    /**
     * @var array
     */
    private $arrayData = [];

    /**
     * @param array|null $arrayData
     * @return $this|array
     */
    public function data(array $arrayData = null)
    {
        if (
            !is_null($arrayData)
            && is_array($arrayData)
        ) {
            $this->arrayData = $arrayData;
            return $this;
        }
        return $this->arrayData;
    }

    /**
     * @return bool
     */
    public function hasPassed() {
        return $this->errors()->isEmpty();
    }

    /**
     * @var
     */
    private $closureOnPass;

    /**
     * @param \Closure $callable
     * @return $this
     */
    public function onPass(\Closure $callable) {
        // Add the closure to the OnPass
        $this->closureOnPass = $callable;

        // Return this for chaining
        return $this;
    }

    /**
     * @return bool
     */
    public function hasFailed() {
        return !$this->errors()->isEmpty();
    }

    /**
     * @var
     */
    private $closureOnFail;

    /**
     * @param \Closure $callable
     * @return $this
     */
    public function onFail(\Closure $callable) {
        // Add the closure to the OnFail
        $this->closureOnFail = $callable;

        // Return this for chaining
        return $this;
    }

    /**
     * @return $this|mixed
     */
    public function validate()
    {
        // Loop through all the Conditions
        $this->conditions()->each(function(Condition $condition) {
            // Loop through all the Rules for this Condition
            $condition->rules()->each(function(Rule $rule) use ($condition) {
                // Get the validator as an array which should contain a function and reasons language array
                $validator = $this->validators()->get($rule->validator());

                // Check if the value stored in the data for the condition's key meets the requirements for the specific
                // validator and parameters
                if (
                    (
                        !(
                            $boolResult = $validator->execute([
                                'data' => $this->data(),
                                'key' => $condition->key(),
                                'value' => array_get($this->data(), $condition->key(), null),
                                'validator' => $rule->validator(),
                                'parameters' => $rule->parameters()
                            ])
                        )
                            && !$rule->reverse()
                    )
                    || (
                        $boolResult
                        && $rule->reverse()
                    )
                ) {
                    // Define the new Error Reason
                    $reason = new Reason([
                        'key' => (
                            $stringMessage = Templator::parse(
                                (
                                    ($language = $validator->languages()->get($this->language())) instanceof Language
                                        ? (
                                            !$rule->reverse()
                                                ? $language->normal()
                                                : $language->reverse()
                                        )
                                        : 'validation failed without reason'
                                ),
                                [
                                    'data' => $this->data(),
                                    'key' => $condition->key(),
                                    'value' => array_get($this->data(), $condition->key(), null),
                                    'validator' => $rule->validator(),
                                    'parameters' => $rule->parameters()
                                ]
                            )
                        ),
                        'message' => $stringMessage
                    ]);

                    // Check if we found an existing error
                    if (
                        (
                            $error = $this->errors()->get($condition->key(), null)
                        ) instanceof Error
                    ) {
                        // Add the new Reason to the Error
                        $error->reasons()->set($reason->key(), $reason);
                    } else {
                        // Create a new Error with a reason
                        $this->errors()->set(
                            $condition->key(),
                            new Error([
                                'key' => $condition->key(),
                                'reasons' => new ReasonsContainer([
                                    $reason->key() => $reason
                                ])
                            ])
                        );
                    }
                }
            });
        });

        // Check if hasPassed and if we have an OnPass closure and execute closure
        if (
            $this->hasPassed()
            && $this->closureOnPass instanceof \Closure
        ) {
            return call_user_func_array(
                $this->closureOnPass,
                [
                    $this
                ]
            );
        }

        // Check if hasFailed and if we have an OnFail closure and execute closure
        if (
            $this->hasFailed()
            && $this->closureOnFail instanceof \Closure
        ) {
            return call_user_func_array(
                $this->closureOnFail,
                [
                    $this
                ]
            );
        }

        // Return this for chaining if not already returned for OnPass or OnFail closures
        return $this;
    }

    /**
     * @return array
     */
    public function dump()
    {
        return $this();
    }
}