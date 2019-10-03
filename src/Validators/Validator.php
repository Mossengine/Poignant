<?php
namespace Mossengine\Poignant\Validators;

use Mossengine\Poignant\Extensions\Item;
use Mossengine\Poignant\Validators\Languages\LanguagesContainer;

/**
 * Class Item
 * @package Mossengine\Poignant\Extensions
 */
class Validator extends Item
{
    /**
     * Validator constructor.
     * @param array $arrayParameters
     */
    public function __construct($arrayParameters = [])
    {
        // Call the parent to satisfy OOP
        parent::__construct($arrayParameters);

        // languages
        $this->languages(new LanguagesContainer(
            array_get($arrayParameters, 'languages', [])
        ));

        // closure
        $this->closure(array_get($arrayParameters, 'closure'));
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'languages' => $this->languages,
            'closure' => $this->closure()
        ];
    }

    /**
     * @param null $containerLanguages
     * @return $this|mixed
     */
    public function languages($containerLanguages = null)
    {
        if (
            !is_null($containerLanguages)
            && ($containerLanguages instanceof LanguagesContainer)
        ) {
            $this->attributeSet('languages', $containerLanguages);
            return$this;
        }
        return $this->attributeGet('languages', new LanguagesContainer());
    }

    /**
     * @param null $closure
     * @return $this|mixed
     */
    public function closure($closure = null)
    {
        if (
            !is_null($closure)
            && ($closure instanceof \Closure)
        ) {
            $this->attributeSet('closure', $closure);
            return$this;
        }
        return $this->attributeGet('closure', function() { return null; });
    }

    /**
     * @param array $arrayParameters
     * @return mixed
     */
    public function execute($arrayParameters = [])
    {
        return call_user_func(
            $this->closure(),
            $arrayParameters
        );
    }
}