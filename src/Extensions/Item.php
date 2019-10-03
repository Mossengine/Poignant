<?php
namespace Mossengine\Poignant\Extensions;

/**
 * Class Item
 * @package Mossengine\Poignant\Extensions
 */
class Item
{
    /**
     * Item constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Set the key into the key store for item classes
        $this->key(array_get($arrayParameters, 'key', null));

        // Set the constructor parameters into the parameters array
        $this->attributes($arrayParameters);
    }

    /**
     * @param $stringAttribute
     * @return |null
     */
    public function __get($stringAttribute)
    {
        if (
            method_exists($this, $stringAttribute)
            && method_exists(($invokable = $this->{$stringAttribute}()), '__invoke')
        ) {
            return $invokable();
        }

        return null;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->arrayAttributes;
    }

    /**
     * @param null $stringKey
     * @return $this|mixed
     */
    public function key($stringKey = null) {
        if (
            !is_null($stringKey)
            && is_string($stringKey)
        ) {
            $this->attributeSet('key', $stringKey);
            return $this;
        }
        return $this->attributeGet('key');
    }

    /**
     * @var array
     */
    private $arrayAttributes = [];

    /**
     * @param null $arrayAttributes
     * @return $this|array
     */
    public function attributes($arrayAttributes = null) {
        if (
            !is_null($arrayAttributes)
            && is_array($arrayAttributes)
        ) {
            $this->arrayAttributes = $arrayAttributes;
            return $this;
        }
        return $this->arrayAttributes;
    }

    /**
     * @param $stringKey
     * @param null $mixedDefault
     * @return mixed
     */
    public function attributeGet($stringKey, $mixedDefault = null) {
        return array_get($this->arrayAttributes, $stringKey, $mixedDefault);
    }

    /**
     * @param $stringKey
     * @param $mixedValue
     */
    public function attributeSet($stringKey, $mixedValue) {
        array_set($this->arrayAttributes, $stringKey, $mixedValue);
    }

    /**
     * @return array
     */
    public function dump()
    {
        return $this();
    }
}