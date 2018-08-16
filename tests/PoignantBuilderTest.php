<?php

/**
 * Class PoignantBuilderTest
 */
class PoignantBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testIfConstructable() {
        $classPoignant = new Mossengine\Poignant\Poignant;
        $this->assertTrue($classPoignant instanceof Mossengine\Poignant\Poignant);
        unset($classPoignant);
    }

    public function testIfStaticConstructable() {
        $classPoignant = Mossengine\Poignant\Poignant::create();
        $this->assertTrue($classPoignant instanceof Mossengine\Poignant\Poignant);
        unset($classPoignant);
    }

    public function testIfStaticConstructableWithStructure() {
        $classPoignant = Mossengine\Poignant\Poignant::create([
            'name' => [],
            'age' => []
        ]);
        $this->assertTrue('{"name":[],"age":[]}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }

    public function testBuildSingleWithConditionUndefined() {
        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName();
        $this->assertTrue('{"name":[]}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }

    public function testBuildMultipleWithConditionUndefined() {
        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName()
            ->withAge();
        $this->assertTrue('{"name":[],"age":[]}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }

    public function testBuildSingleWithConditionRequired() {
        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName(function($condition) {
                return $condition->required();
            });
        $this->assertTrue('{"name":{"isset":"parameter must be set","!empty":"parameter must not be empty"}}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }

    public function testBuildSingleWithConditionIsset() {
        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName(function($condition) {
                return $condition->isset();
            });
        $this->assertTrue('{"name":{"isset":"parameter must be set"}}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }

    public function testBuildSingleWithConditionEmpty() {
        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withName(function($condition) {
                return $condition->empty();
            });
        $this->assertTrue('{"name":{"empty":"parameter must be empty"}}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }

    public function testBuildSingleWithConditionEmail() {
        $classPoignant = Mossengine\Poignant\Poignant::create()
            ->withEmail(function($condition) {
                return $condition->email();
            });
        $this->assertTrue('{"email":{"email":"parameter must be a valid email"}}' === json_encode($classPoignant->bag()));
        unset($classPoignant);
    }
}