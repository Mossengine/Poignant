<?php

/**
 * Class Poignant2ValidatorsTest
 */
class Poignant2ValidatorsTest extends PHPUnit_Framework_TestCase
{
    public function testValidatorRequired() {
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

    public function testValidatorNotRequired() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->notRequired();
            });

        $this->assertTrue(
            '5a6c7a32d976f0672e24c6f9e2dda1ac'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorIsset() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->isset();
            });

        $this->assertTrue(
            'e86564ad1701573ad5231f3726f7b74f'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorEmpty() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->empty();
            });

        $this->assertTrue(
            '5a8b50ae217722300329116418f4a6b4'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorUuid() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->uuid();
            });

        $this->assertTrue(
            'd74fdd1d0a9d6ea898f224b8ad234a03'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorEmail() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->email();
            });

        $this->assertTrue(
            '980f8a81dedbe9e262fa3539c34773ec'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorString() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->string();
            });

        $this->assertTrue(
            '89fc778808156d768982ed55002f267a'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorArray() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->array();
            });

        $this->assertTrue(
            '29b0013c9464e15e926ca3ae077f6c3a'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorObject() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->object();
            });

        $this->assertTrue(
            'c2ea957fed962c081d5882e2457f4cfe'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorNumeric() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->numeric();
            });

        $this->assertTrue(
            'a97ba81bd4595b1007bd1625ed90e5bc'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorInteger() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->integer();
            });

        $this->assertTrue(
            '9e76789eaab91d967f6cacbeb9122a8c'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorFloat() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->float();
            });

        $this->assertTrue(
            '096c80b2d775b46829f892af6a0a8f21'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorCarbon() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->carbon('Y-m-d');
            });

        $this->assertTrue(
            '1900d0fcf39623894e3c111d81939c79'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorDatetime() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->datetime();
            });

        $this->assertTrue(
            'c8a5a9be24dbf28cbcb31f060984fad2'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorDate() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->date();
            });

        $this->assertTrue(
            '651445325490af33cd82e935575947ad'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorTime() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->time();
            });

        $this->assertTrue(
            'bea8b23c0ad2c1a0d2ddb2081fff94c1'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorBoolean() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->boolean();
            });

        $this->assertTrue(
            '4a7bf4ab5304275f88a46f609be8ad04'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorTrue() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->true();
            });

        $this->assertTrue(
            '168bd0ef56b4ba35eb372d4e86e601eb'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorFalse() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withName(function($condition) {
                $condition->false();
            });

        $this->assertTrue(
            '8f6c2d0cf8f794a4ce26e911525a4c3b'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorIn() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withA(function($condition) {
                $condition->in(['a','b','c'])
                    ->notIn(['x','y','z']);
            })
            ->withB(function($condition) {
                $condition->in('a,b,c')
                    ->notIn('x,y,3');
            })
            ->withC(function($condition) {
                $condition->in(['a','b',5])
                    ->notIn(['x','y',3]);
            });

        $this->assertTrue(
            'd2fd90948c1f88fa20db560de5da0a7f'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorLength() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withA(function($condition) {
                $condition->length('lt', 5)
                ->length('<', 5);
            })
            ->withB(function($condition) {
                $condition->length('lte', 10)
                    ->length('elt', 10)
                    ->length('<=', 10)
                    ->length('=<', 10);
            })
            ->withC(function($condition) {
                $condition->length('eq', 15)
                    ->length('=', 15)
                    ->length('==', 15)
                    ->length('===', 15);
            })
            ->withE(function($condition) {
                $condition->length('gte', 25)
                    ->length('egt', 25)
                    ->length('>=', 25)
                    ->length('=>', 25);
            })
            ->withD(function($condition) {
                $condition->length('gt', 20)
                    ->length('>', 20);
            });

        $this->assertTrue(
            '245574220d8caba263208989c28894c8'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }

    public function testValidatorCompare() {
        $classPoignant2 = Mossengine\Poignant\Poignant2::create()
            ->withA(function($condition) {
                $condition->compare('lt', 5)
                    ->compare('<', 5);
            })
            ->withB(function($condition) {
                $condition->compare('lte', 10)
                    ->compare('elt', 10)
                    ->compare('<=', 10)
                    ->compare('=<', 10);
            })
            ->withC(function($condition) {
                $condition->compare('eq', 15)
                    ->compare('=', 15)
                    ->compare('==', 15);
            })
            ->withC(function($condition) {
                $condition->compare('identical', 15.5)
                    ->compare('same', 15.5)
                    ->compare('===', 15.5);
            })
            ->withE(function($condition) {
                $condition->compare('gte', 25)
                    ->compare('egt', 25)
                    ->compare('>=', 25)
                    ->compare('=>', 25);
            })
            ->withD(function($condition) {
                $condition->compare('gt', 20)
                    ->compare('>', 20);
            });

        $this->assertTrue(
            '18fbfc3b61c76e1b67df0fa1cb2a3dda'
            === md5(json_encode($classPoignant2->conditions))
        );
        unset($classPoignant2);
    }
}