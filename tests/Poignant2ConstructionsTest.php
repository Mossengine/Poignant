<?php

/**
 * Class Poignant2ConstructionsTest
 */
class Poignant2ConstructionsTest extends PHPUnit_Framework_TestCase
{
    public function testIfConstructable() {
        $classPoignant2 = new Mossengine\Poignant\Poignant2;
        $this->assertTrue($classPoignant2 instanceof Mossengine\Poignant\Poignant2);
        unset($classPoignant2);
    }

    public function testIfStaticConstructable() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create();
        $this->assertTrue($classPoignant2 instanceof Mossengine\Poignant\Poignant2);
        unset($classPoignant2);
    }

    public function testConstructionWithConditions() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create([
            'conditions' => [
                'name' => [],
                'age' => []
            ]
        ]);

        $this->assertTrue(
            '6f33156835f8f7878602c423d0b60f2f'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testConstructionWithConditionsWithRules() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create([
            'conditions' => [
                'name' => [
                    'rules' => [
                        'required'
                    ]
                ],
                'age' => [
                    'rules' => [
                        'string',
                        'length|>|5'
                    ]
                ]
            ]
        ]);

        $this->assertTrue(
            '8f871567f9e21179d0cdb3087fa539e8'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testConstructionWithConditionsContainer() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create([
            'conditions' => new \Mossengine\Poignant\Conditions\ConditionsContainer([
                'name' => [
                    'rules' => new \Mossengine\Poignant\Conditions\Rules\RulesContainer([
                        'required'
                    ])
                ],
                'age' => [
                    'rules' => new \Mossengine\Poignant\Conditions\Rules\RulesContainer([
                        'string',
                        'length|>|5'
                    ])
                ]
            ])
        ]);

        $this->assertTrue(
            '8f871567f9e21179d0cdb3087fa539e8'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testConstructionWithConstruction() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create([
            'conditions' => new \Mossengine\Poignant\Conditions\ConditionsContainer([
                'name' => [
                    'rules' => new \Mossengine\Poignant\Conditions\Rules\RulesContainer([
                        'required'
                    ])
                ],
                'age' => [
                    'rules' => new \Mossengine\Poignant\Conditions\Rules\RulesContainer([
                        'string',
                        'length|>|5'
                    ])
                ]
            ])
        ]);
        $classPoignant2 = Mossengine\Poignant\Poignant2::create($classPoignant2());

        $this->assertTrue(
            '8f871567f9e21179d0cdb3087fa539e8'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }
}