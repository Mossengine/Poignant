<?php

/**
 * Class Poignant2ValidationsTest
 */
class Poignant2ValidationsTest extends PHPUnit_Framework_TestCase
{
    public function testCondition() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName();

        $this->assertTrue(
            'b84082cd31c9b123bd0a632b63b5c287'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testConditions() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName()
            ->withAge();

        $this->assertTrue(
            '6f33156835f8f7878602c423d0b60f2f'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testConditionWithOneRule() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->required();
            });

        $this->assertTrue(
            '0494268ed4dbf84d7a3312518746f5e2'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testConditionsWithRules() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withAge(function($condition) {
                $condition->isset()
                    ->integer();
            });

        $this->assertTrue(
            'cf9631f350e101776f815185bc32611c'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }
}