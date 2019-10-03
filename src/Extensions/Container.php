<?php
namespace Mossengine\Poignant\Extensions;

/**
 * Class Container
 * @package Mossengine\Poignant\Extensions
 */
class Container
{
    /**
     * Container constructor.
     * @param null $mixedFill
     */
    public function __construct($mixedFill = null)
    {
        // Merge in any fill data
        $this->merge(
            is_array($mixedFill)
                ? $mixedFill
                : (
                    $mixedFill instanceof self
                        ? $mixedFill()
                        : []
            )
        );
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return array_combine(
            $this->map(function($item) {
                return $item->key();
            }),
            $this->map(function($item) {
                return $item();
            })
        );
    }

    /**
     * @var string
     */
    protected $classItem = Item::class;

    /**
     * @var array
     */
    protected $arrayContainer = [];

    /**
     * @return mixed
     */
    public function index()
    {
        // Return the container
        return $this();
    }

    /**
     * @param $stringKey
     * @param null $mixedDefault
     * @return mixed
     */
    public function get($stringKey, $mixedDefault = null)
    {
        // Return the item or array of items found at the defined key else provide the defined default
        return array_get($this->arrayContainer, md5($stringKey), $mixedDefault);
    }

    /**
     * @param $stringKey
     * @param array $mixedItem
     * @return $this
     */
    public function set($stringKey, &$mixedItem = [])
    {
        // only accept array or instance of item
        if (
            !is_array($mixedItem)
            && !($mixedItem instanceof $this->classItem)
        ) {
            return $this;
        }

        // If we're about to parse an array of data into the item then set the key in the array so that the item
        // constructor can define the key within the item class
        if (is_array($mixedItem)) {
            array_set($mixedItem, 'key', $stringKey);
        }

        // Ensure we have an instantiation of the intended class
        $mixedItem = (
            $mixedItem instanceof $this->classItem
                ? $mixedItem
                : new $this->classItem($mixedItem)
        );

        // We're just setting, no multiple merging
        array_set(
            $this->arrayContainer,
            md5(
                $mixedItem->key()
            ),
            $mixedItem
        );

        // Return this for chaining
        return $this;
    }

    /**
     * @param $stringKey
     * @return $this
     */
    public function forget($stringKey)
    {
        // Forget any item or multiples or items at a given key
        array_forget($this->arrayContainer, md5($stringKey));

        // Return this for chaining
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        // Clear the entire container back to nothing
        $this->arrayContainer = [];

        // Return this for chaining
        return $this;
    }

    /**
     * @param array|null $arrayFill
     * @return $this
     */
    public function merge(array $arrayFill = null)
    {
        // If the fill is an array then proceed to fill the items in using the set function
        if (
            !is_null($arrayFill)
            && is_array($arrayFill)
            && !empty(
                $arrayFill = (
                    array_keys($arrayFill) !== range(0, count($arrayFill) - 1)
                        ? $arrayFill
                        : call_user_func_array(
                            'array_combine',
                            [
                                (
                                    array_map(
                                        function($arrayItem) {
                                            return array_get($arrayItem, 0);
                                        },
                                        (
                                            $arrayFill = array_filter(
                                                array_map(
                                                    function($mixedItem) {
                                                        return (
                                                        (
                                                            !is_array($mixedItem)
                                                            && is_string($mixedItem)
                                                            && !empty($stringKey = $mixedItem)
                                                        )
                                                        || (
                                                            is_array($mixedItem)
                                                            && is_string($stringKey = array_get($mixedItem, 'key', null))
                                                            && !empty($stringKey)
                                                        )
                                                            ? [
                                                                $stringKey,
                                                                (
                                                                    is_array($mixedItem)
                                                                        ? $mixedItem
                                                                        : []
                                                                )
                                                            ]
                                                            : null
                                                        );
                                                    },
                                                    $arrayFill
                                                )
                                            )
                                        )
                                    )
                                ),
                                array_map(
                                    function($arrayItem) {
                                        return array_get($arrayItem, 1);
                                    },
                                    $arrayFill
                                )
                            ]
                        )
                )
            )
        ) {
            // Loop over each item
            foreach ($arrayFill as $stringKey => $mixedItem) {
                $this->set($stringKey, $mixedItem);
            }
        }

        // Return this for chaining
        return $this;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->arrayContainer);
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function each(\Closure $closure)
    {
        // Loop over the container and pass in each item to a closure defined by the caller of this function
        foreach ($this->arrayContainer as $item) {
            // execute the function and pass in the item as first parameter
            call_user_func_array($closure, [$item]);
        }

        // Return this for chaining
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return array
     */
    public function map(\Closure $closure)
    {
        // Generate an array to return based on mapping the items from the container into a function that calls the
        // closure made by the caller of this function
        return array_values(
            array_map(
                function($item) use ($closure) {
                    // return the output of the closure into the mapped array
                    return call_user_func_array($closure, [$item]);
                },
                $this->arrayContainer
            )
        );
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function transform(\Closure $closure)
    {
        // Loop over each item in the container but reference the item so that changes made to the item are retained
        foreach ($this->arrayContainer as &$item) {
            // call the closure defined by the caller of this function and return the output back into the item that is
            // reference.
            $item = call_user_func_array($closure, [$item]);
        }

        // Return this for chaining
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        // Return the boolean result of performing an empty check on the container of items
        return empty($this->arrayContainer);
    }

    /**
     * @return array
     */
    public function dump()
    {
        return $this();
    }
}