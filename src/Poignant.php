<?php
namespace Mossengine\Poignant;

/**
 * Class Poignant
 * @package Mossengine\Poignant
 */
class Poignant
{
    /**
     * @var array
     */
    public $arrayConditions = [];

    /**
     * @var null
     */
    private $boolValid = null;

    /**
     * @var array
     */
    private $arrayValidation = [];

    /**
     * @var
     */
    private $isValidResults;

    /**
     * @var
     */
    private $isInvalidResults;

    /**
     * Poignant constructor.
     * @param array|null $arrayConditions
     */
    public function __construct(Array $arrayConditions = null) {
        $this->bag($arrayConditions);
    }

    /**
     * @param null $arrayConditions
     * @return array|null
     */
    public function bag($arrayConditions = null) {
        return $this->arrayConditions = (null !== $arrayConditions ? $arrayConditions : $this->arrayConditions);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function add($key, $value) {
        $this->arrayConditions[$key] = (isset($this->arrayConditions[$key]) ? array_merge($this->arrayConditions[$key], $value) : $value);
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    protected function remove($key) {
        unset($this->arrayConditions[$key]);
        return $this;
    }

    /**
     * @param array|null $arrayConditions
     * @return Poignant
     */
    public static function create(Array $arrayConditions = null) {
        return new Poignant($arrayConditions);
    }

    /**
     * @param $stringFunction
     * @param $arrayArguments
     * @return mixed
     */
    public static function __callStatic($stringFunction, $arrayArguments) {
        $poignant = new Poignant((is_array(array_get($arrayArguments, 0, null) ? array_get($arrayArguments, 0, null) : null)));
        return call_user_func_array([$poignant, $stringFunction], $arrayArguments);
    }

    /**
     * @param $stringMethod
     * @param $arrayArguments
     * @return Poignant
     */
    public function __call($stringMethod, $arrayArguments) {
        // Check for 'with' prefix
        if ('with' === strtolower(substr($stringMethod, 0, 4))) {
            // Get the condition key from the string method name
            $stringConditionKey = strtolower(substr($stringMethod, 4));

            // Check for condition key length > 0
            if (strlen($stringConditionKey) > 0) {
                // Replace all _ with . for dot notation key depth
                $stringConditionKey = str_replace('_', '.', $stringConditionKey);

                // create the condition based on either calling the callable with first parameters as the Condition class or an empty array as no callable was provided.
                $condition = (is_callable(array_get($arrayArguments, 0, null)) ? call_user_func_array(array_get($arrayArguments, 0, null), [new Condition()]) : []);
                
                // Return this from the addition to the conditions bag
                return $this->add($stringConditionKey, ($condition instanceof Condition ? $condition->bag() : $condition));
            }
        }

        // Return this as there was no magic functions...
        return $this;
    }

    /**
     * function to validate data array based on validation rules inside the builder
     *
     * @param $arrayData
     * @return $this
     *
     * each key in the validation array is a dot separated path to the value inside the data array
     * the key then has an array with eah key in that array being a validation rule where the value is the error msg
     *
     * example validation array:
     * [
     *     'payload.username' => [
     *         'isset' => 'You need to define a username under payload.username',
     *         '!empty' => 'Your username cannot be empty'
     *     ],
     *     'payload.password' => [
     *         'isset' => 'You need to define a password under payload.password',
     *         '!empty' => 'Your password cannot be empty'
     *         'length|>|7' => 'Your password needs to be greater than 7 characters in length'
     *     ]
     * ]
     */
    public function validate($arrayData) {
        // Define the return array
        $this->arrayValidation = [];

        foreach ($this->bag() as $stringRuleKey => $arrayRuleConditions) {
            foreach ($arrayRuleConditions as $stringRule => $stringReason) {
                $arrayRuleParameters = explode('|', $stringRule);

                switch (array_get($arrayRuleParameters, 0, null)) {
                    case 'isset':
                        $stringNotIsset = md5(rand(1,999) . '_' . time() . '_' . '!isset' . '_' . rand(1,999));
                        if ($stringNotIsset === array_get($arrayData, $stringRuleKey, $stringNotIsset)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!isset':
                        $stringNotIsset = md5(rand(1,999) . '_' . time() . '_' . '!isset' . '_' . rand(1,999));
                        if ($stringNotIsset !== array_get($arrayData, $stringRuleKey, $stringNotIsset)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'empty':
                        if (!empty(array_get($arrayData, $stringRuleKey)) || false === array_get($arrayData, $stringRuleKey)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!empty':
                        if (false !== array_get($arrayData, $stringRuleKey) && empty(array_get($arrayData, $stringRuleKey))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'email':
                        if (isset($arrayData[$stringRuleKey]) && false === filter_var(array_get($arrayData, $stringRuleKey), FILTER_VALIDATE_EMAIL)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!email':
                        if (isset($arrayData[$stringRuleKey]) && false !== filter_var(array_get($arrayData, $stringRuleKey), FILTER_VALIDATE_EMAIL)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'boolean':
                        if (isset($arrayData[$stringRuleKey]) && !is_bool(array_get($arrayData, $stringRuleKey))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!boolean':
                        if (isset($arrayData[$stringRuleKey]) && is_bool(array_get($arrayData, $stringRuleKey))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'string':
                        if (isset($arrayData[$stringRuleKey]) && !is_string(array_get($arrayData, $stringRuleKey, 'string'))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!string':
                        if (isset($arrayData[$stringRuleKey]) && is_string(array_get($arrayData, $stringRuleKey, 1234))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'numeric':
                        if (isset($arrayData[$stringRuleKey]) && !is_numeric(array_get($arrayData, $stringRuleKey, 1234))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!numeric':
                        if (isset($arrayData[$stringRuleKey]) && is_numeric(array_get($arrayData, $stringRuleKey, 'string'))) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'datetime':
                        if (isset($arrayData[$stringRuleKey]) && array_get($arrayData, $stringRuleKey, 'invalid datetime string') !== \Carbon\Carbon::parse(array_get($arrayData, $stringRuleKey, null))->toDateTimeString()) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!datetime':
                        if (isset($arrayData[$stringRuleKey]) && array_get($arrayData, $stringRuleKey, 'invalid datetime string') === \Carbon\Carbon::parse(array_get($arrayData, $stringRuleKey, null))->toDateTimeString()) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'true':
                        if (isset($arrayData[$stringRuleKey]) && true !== array_get($arrayData, $stringRuleKey, false)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!true':
                        if (isset($arrayData[$stringRuleKey]) && true === array_get($arrayData, $stringRuleKey, true)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'false':
                        if (isset($arrayData[$stringRuleKey]) && false !== array_get($arrayData, $stringRuleKey, true)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case '!false':
                        if (isset($arrayData[$stringRuleKey]) && false === array_get($arrayData, $stringRuleKey, false)) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    case 'length':
                        if (isset($arrayRuleParameters[1]) && isset($arrayRuleParameters[2])) {
                            switch ($arrayRuleParameters[1]) {
                                case '<':
                                    if (isset($arrayData[$stringRuleKey]) && !(strlen(array_get($arrayData, $stringRuleKey)) < intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '<=':
                                    if (isset($arrayData[$stringRuleKey]) && !(strlen(array_get($arrayData, $stringRuleKey)) <= intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '=':
                                    if (isset($arrayData[$stringRuleKey]) && !(strlen(array_get($arrayData, $stringRuleKey)) === intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>=':
                                    if (isset($arrayData[$stringRuleKey]) && !(strlen(array_get($arrayData, $stringRuleKey)) >= intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>':
                                    if (isset($arrayData[$stringRuleKey]) && !(strlen(array_get($arrayData, $stringRuleKey)) > intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                            }
                        }
                        continue;
                    case 'compare':
                        if (isset($arrayRuleParameters[1]) && isset($arrayRuleParameters[2])) {
                            switch ($arrayRuleParameters[1]) {
                                case '==':
                                    if (isset($arrayData[$stringRuleKey]) && !(array_get($arrayData, $stringRuleKey) == array_get($arrayData, $arrayRuleParameters[2], $arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '===':
                                    if (isset($arrayData[$stringRuleKey]) && !(array_get($arrayData, $stringRuleKey) === array_get($arrayData, $arrayRuleParameters[2], $arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '!=':
                                    if (isset($arrayData[$stringRuleKey]) && !(array_get($arrayData, $stringRuleKey) != array_get($arrayData, $arrayRuleParameters[2], $arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '!==':
                                    if (isset($arrayData[$stringRuleKey]) && !(array_get($arrayData, $stringRuleKey) !== array_get($arrayData, $arrayRuleParameters[2], $arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) > floatval(array_get($arrayData, $arrayRuleParameters[2])))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>=':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) >= floatval(array_get($arrayData, $arrayRuleParameters[2])))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '<':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) < floatval(array_get($arrayData, $arrayRuleParameters[2])))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '<=':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) <= floatval(array_get($arrayData, $arrayRuleParameters[2])))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                            }
                        }
                        continue;
                    case 'array':
                        if (isset($arrayRuleParameters[1]) && isset($arrayRuleParameters[2])) {
                            switch ($arrayRuleParameters[1]) {
                                case 'in':
                                    if (isset($arrayData[$stringRuleKey]) && !in_array(array_get($arrayData, $stringRuleKey, 'null'), explode(',', $arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '!in':
                                    if (isset($arrayData[$stringRuleKey]) && in_array(array_get($arrayData, $stringRuleKey, 'null'), explode(',', $arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                            }
                        }
                        continue;
                    case 'intval':
                        if (isset($arrayRuleParameters[1]) && isset($arrayRuleParameters[2])) {
                            switch ($arrayRuleParameters[1]) {
                                case '<':
                                    if (isset($arrayData[$stringRuleKey]) && !(intval(array_get($arrayData, $stringRuleKey)) < intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '<=':
                                    if (isset($arrayData[$stringRuleKey]) && !(intval(array_get($arrayData, $stringRuleKey)) <= intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '=':
                                    if (isset($arrayData[$stringRuleKey]) && !(intval(array_get($arrayData, $stringRuleKey)) === intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>=':
                                    if (isset($arrayData[$stringRuleKey]) && !(intval(array_get($arrayData, $stringRuleKey)) >= intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>':
                                    if (isset($arrayData[$stringRuleKey]) && !(intval(array_get($arrayData, $stringRuleKey)) > intval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                            }
                        }
                        continue;
                    case 'floatval':
                        if (isset($arrayRuleParameters[1]) && isset($arrayRuleParameters[2])) {
                            switch ($arrayRuleParameters[1]) {
                                case '<':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) < floatval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '<=':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) <= floatval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '=':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) === floatval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) > floatval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                                case '>=':
                                    if (isset($arrayData[$stringRuleKey]) && !(floatval(array_get($arrayData, $stringRuleKey)) >= floatval($arrayRuleParameters[2]))) {
                                        $this->arrayValidation[$stringRuleKey][] = $stringReason;
                                    }
                                    continue;
                            }
                        }
                        continue;
                    case 'meta':
                        if (isset($arrayData[$stringRuleKey]) && true !== is_array($arrayData[$stringRuleKey])) {
                            $this->arrayValidation[$stringRuleKey][] = $stringReason;
                        }
                        continue;
                    default:
                        if (is_callable($stringReason)) {
                            if (true !== $stringCallableReason = call_user_func_array($stringReason, [array_get($arrayData, $stringRuleKey)])) {
                                $this->arrayValidation[$stringRuleKey][] = $stringCallableReason;
                            }
                        }
                        continue;
                }
            }
        }

        // Return true if no invalid
        $this->boolValid = empty($this->arrayValidation);

        return $this;
    }

    /**
     * @param array $arrayData
     * @param $callable
     * @return $this|bool|null
     */
    public function onPass(Array $arrayData, $callable) {
        if (is_null($this->boolValid)) {
            $this->validate($arrayData);
        }

        if ($this->boolValid) {
            $this->isValidResults = call_user_func_array($callable, [$this->arrayValidation]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPassed() {
        return (!is_null($this->boolValid) && true === $this->boolValid);
    }

    /**
     * @param array $arrayData
     * @param $callable
     * @return $this
     */
    public function onFail(Array $arrayData, $callable) {
        if (is_null($this->boolValid)) {
            $this->validate($arrayData);
        }

        if (!$this->boolValid) {
            $this->isInvalidResults = call_user_func_array($callable, [$this->arrayValidation]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFailed() {
        return (!is_null($this->boolValid) && false === $this->boolValid);
    }

    /**
     * @return array|bool
     */
    public function getResults() {
        return (!empty($this->isValidResults) || !empty($this->isInvalidResults) ? [
            'valid' => $this->isValidResults,
            'invalid' => $this->isInvalidResults
        ] : false);
    }
}