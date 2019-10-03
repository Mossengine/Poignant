<?php
namespace Mossengine\Poignant\Errors;

use Mossengine\Poignant\Errors\Reasons\ReasonsContainer;
use Mossengine\Poignant\Extensions\Item;

/**
 * Class Item
 * @package Mossengine\Poignant\Extensions
 */
class Error extends Item
{
    /**
     * Error constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Call the parent to satisfy OOP
        parent::__construct($arrayParameters);

        // reasons
        $this->reasons(new ReasonsContainer(
            array_get($arrayParameters, 'reasons', [])
        ));
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'reasons' => $this->reasons
        ];
    }

    /**
     * @param null $containerReasons
     * @return $this|mixed
     */
    public function reasons($containerReasons = null)
    {
        if (
            !is_null($containerReasons)
            && ($containerReasons instanceof ReasonsContainer)
        ) {
            $this->attributeSet('reasons', $containerReasons);
            return$this;
        }
        return $this->attributeGet('reasons', new ReasonsContainer());
    }
}