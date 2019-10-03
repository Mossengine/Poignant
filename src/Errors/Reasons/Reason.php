<?php
namespace Mossengine\Poignant\Errors\Reasons;

use Mossengine\Poignant\Extensions\Item;

/**
 * Class Reason
 * @package Mossengine\Poignant\Errors\Reasons
 */
class Reason extends Item
{
    /**
     * Rule constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Call the parent to satisfy OOP
        parent::__construct($arrayParameters);

        // message
        $this->message(array_get($arrayParameters, 'message', 'No reason'));
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return 'aaa';
//        return [
//            'attributes' => $this->attributes(),
//            'message' => $this->attributeGet('message')
//        ];
    }

    /**
     * @param null $strinMessage
     * @return $this|mixed
     */
    public function message($strinMessage = null) {
        if (
            !is_null($strinMessage)
            && is_string($strinMessage)
        ) {
            $this->attributeSet('message', $strinMessage);
            return$this;
        }
        return $this->attributeGet('message');
    }
}