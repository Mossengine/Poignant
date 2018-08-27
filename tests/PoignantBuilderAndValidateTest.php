<?php

/**
 * Class PoignantBuilderAndValidateTest
 */
class PoignantBuilderAndValidateTest extends PHPUnit_Framework_TestCase
{
    public function testBuildSingleWithConditionRequiredAndValidateFail() {
        $results = null;

        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName(function($condition) {
                return $condition->required();
            })
            ->onFail([], function($mixedResults) use (&$results) {
                $results = $mixedResults;
                return 'fail';
            });

        $this->assertTrue($classPoignant->hasFailed() && '{"name":["parameter must be set","parameter must not be empty"]}' === json_encode($results) && '"fail"' === json_encode($classPoignant->getResults()));
        unset($classPoignant);
    }

    public function testBuildSingleWithConditionRequiredAndValidatePass() {
        $results = null;

        $arrayData = [
            'name' => 'Tom'
        ];

        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName(function($condition) {
                return $condition->required();
            })
            ->onFail($arrayData, function($mixedResults) use (&$results) {
                $results = $mixedResults;
                return 'fail';
            })
            ->onPass($arrayData, function($mixedResults) use (&$results) {
                $results = $mixedResults;
                return 'pass';
            });

        $this->assertTrue($classPoignant->hasPassed() && empty($results) && '"pass"' === json_encode($classPoignant->getResults()));
        unset($classPoignant);
    }
}