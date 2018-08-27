<?php
namespace Mossengine\Poignant;

/**
 * Class Condition
 * @package Mossengine\Poignant
 */
class Condition
{
    /**
     * @var array
     */
    private $arrayBag = [];

    /**
     * @param null $arrayBag
     * @return array|null
     */
    public function bag($arrayBag = null) {
        return $this->arrayBag = (null !== $arrayBag ? $arrayBag : $this->arrayBag);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function add($key, $value) {
        $this->arrayBag[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    protected function remove($key) {
        unset($this->arrayBag[$key]);
        return $this;
    }

    /**
     * @param $stringMethod
     * @param $arrayArguments
     * @return Condition
     */
    public function __call($stringMethod, $arrayArguments) {
        switch($stringMethod) {
            case 'required':
                return $this->isset(false !== array_get($arrayArguments, 0, true))->empty(false === array_get($arrayArguments, 0, true));
            case 'isset':
                return $this->add((false === array_get($arrayArguments, 0, true) ? '!' : '') . $stringMethod, 'parameter must ' . (false === array_get($arrayArguments, 0, true) ? 'not ' : '') . 'be set');
            case 'empty':
                return $this->add((false === array_get($arrayArguments, 0, true) ? '!' : '') . $stringMethod, 'parameter must ' . (false === array_get($arrayArguments, 0, true) ? 'not ' : '') . 'be empty');
            case 'email':
            case 'boolean':
            case 'string':
            case 'numeric':
            case 'datetime':
            case 'true':
            case 'false':
                return $this->add((false === array_get($arrayArguments, 0, true) ? '!' : '') . $stringMethod, 'parameter must ' . (false === array_get($arrayArguments, 0, true) ? 'not ' : '') . 'be a valid ' . $stringMethod);
            default:
                return $this->add(strtolower($stringMethod), array_get($arrayArguments, 0));
        }
    }

    /**
     * @return Condition
     */
    public function uuid() {
        return $this->add('length|=|36', 'parameter must equal to 36 characters in length');
    }

    /**
     * @var array
     */
    private $arrayLengthOperators = [
        '>' => 'greater than',
        '<' => 'less than',
        '=' => 'equal to',
        '<=' => 'less than or equal to',
        '>=' => 'greater than or equal to'
    ];

    /**
     * @param $stringOperator
     * @param $intLength
     * @return Condition
     */
    public function length($stringOperator, $intLength) {
        $stringOperator = array_has($this->arrayLengthOperators, $stringOperator) ? $stringOperator : '>';
        return $this->add('length|' . $stringOperator . '|' . $intLength, 'parameter must be ' . array_get($this->arrayLengthOperators, $stringOperator) . ' ' . $intLength . ' characters in length');
    }

    /**
     * @var array
     */
    private $arrayCompareOperators = [
        '==' => 'be the same value as',
        '===' => 'be exactly the same type and value as',
        '!=' => 'not be the same value as',
        '!==' => 'not be exactly the same type and value as',
        '>' => 'be greater than a float value of',
        '>=' => 'be greater than or equal to a float value of',
        '<' => 'be less than a float value of',
        '<=' => 'be less than or equal to a float value of'
    ];

    /**
     * @param $stringOperator
     * @param $mixedComparison
     * @return Condition
     */
    public function compare($stringOperator, $mixedComparison) {
        $stringOperator = array_has($this->arrayCompareOperators, $stringOperator) ? $stringOperator : '==';
        return $this->add('compare|' . $stringOperator . '|' . $mixedComparison, 'parameter must ' . array_get($this->arrayCompareOperators, $stringOperator) . ' ' . $mixedComparison);
    }

    /**
     * @param array $arrayItems
     * @param bool $boolNotIn
     * @return Condition
     */
    public function in(Array $arrayItems, $boolNotIn = false) {
        $stringItems = implode(',', $arrayItems);
        return $this->add('array|' . ($boolNotIn ? '!' : '') . 'in|' . $stringItems, 'parameter must ' . ($boolNotIn ? 'not ' : '') . 'be one of the following values [' . $stringItems . ']');
    }

    /**
     * @var array
     */
    private $arrayIntegerFloatOperators = [
        '>' => 'greater than',
        '<' => 'less than',
        '=' => 'equal to',
        '<=' => 'less than or equal to',
        '>=' => 'greater than or equal to'
    ];

    /**
     * @param $stringOperator
     * @param $intValue
     * @return Condition
     */
    public function integer($stringOperator, $intValue) {
        $stringOperator = array_has($this->arrayIntegerFloatOperators , $stringOperator) ? $stringOperator : '>';
        return $this->add('intval|' . $stringOperator . '|' . intval($intValue), 'parameter must be ' . array_get($this->arrayIntegerFloatOperators , $stringOperator) . ' ' . intval($intValue));
    }

    /**
     * @param $stringOperator
     * @param $intValue
     * @return Condition
     */
    public function float($stringOperator, $intValue) {
        $stringOperator = array_has($this->arrayIntegerFloatOperators , $stringOperator) ? $stringOperator : '>';
        return $this->add('floatval|' . $stringOperator . '|' . floatval($intValue), 'parameter must be ' . array_get($this->arrayIntegerFloatOperators , $stringOperator) . ' ' . floatval($intValue));
    }
}