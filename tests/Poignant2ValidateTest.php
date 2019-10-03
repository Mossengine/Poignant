<?php

/**
 * Class Poignant2ValidateTest
 */
class Poignant2ValidateTest extends PHPUnit_Framework_TestCase
{
    public function testValidateFail() {
        list($classPoignant2, $results) = Mossengine\Poignant\Poignant2::create()
            ->withRequired(function($condition) {
                $condition->required();
            })
            ->withIsset(function($condition) {
                $condition->isset();
            })
            ->withEmpty(function($condition) {
                $condition->empty();
            })
            ->withUuid(function($condition) {
                $condition->uuid();
            })
            ->withEmail(function($condition) {
                $condition->email();
            })
            ->withString(function($condition) {
                $condition->string();
            })
            ->withArray(function($condition) {
                $condition->array();
            })
            ->withObject(function($condition) {
                $condition->object();
            })
            ->withNumeric(function($condition) {
                $condition->numeric();
            })
            ->withInteger(function($condition) {
                $condition->integer();
            })
            ->withFloat(function($condition) {
                $condition->float();
            })
            ->withCarbonCarbon(function($condition) {
                $condition->carbon('Y-m-d H:i:s');
            })
            ->withCarbonDatetime(function($condition) {
                $condition->datetime();
            })
            ->withCarbonDate(function($condition) {
                $condition->date();
            })
            ->withCarbonTime(function($condition) {
                $condition->time();
            })
            ->withBoolean(function($condition) {
                $condition->boolean();
            })
            ->withTrue(function($condition) {
                $condition->true();
            })
            ->withFalse(function($condition) {
                $condition->false();
            })
            ->withIn(function($condition) {
                $condition->in(['a','b','c']);
            })
            ->withLength(function($condition) {
                $condition->length('>', 10);
            })
            ->withCompare(function($condition) {
                $condition->compare('===', 45);
            })
            ->onFail(function($classPoignant2) {
                return [$classPoignant2, $classPoignant2->errors];
            })
            ->onPass(function($classPoignant2) {
                return [$classPoignant2, false];
            })
            ->validate();
        echo PHP_EOL . json_encode($results) . PHP_EOL;
        echo PHP_EOL . md5(json_encode($results)) . PHP_EOL;
        $this->assertTrue(
            $classPoignant2->hasFailed()
            && '815c39b29e106dbda686aedaa9aa142e' === md5(json_encode($results))
        );
        unset($classPoignant2);
    }

    public function testValidatePass() {
        list($classPoignant2, $results) = Mossengine\Poignant\Poignant2::create()
            ->withRequired(function($condition) {
                $condition->required();
            })
            ->withIsset(function($condition) {
                $condition->isset();
            })
            ->withEmpty(function($condition) {
                $condition->empty();
            })
            ->withUuid(function($condition) {
                $condition->uuid();
            })
            ->withEmail(function($condition) {
                $condition->email();
            })
            ->withString(function($condition) {
                $condition->string();
            })
            ->withArray(function($condition) {
                $condition->array();
            })
            ->withObject(function($condition) {
                $condition->object();
            })
            ->withNumeric(function($condition) {
                $condition->numeric();
            })
            ->withInteger(function($condition) {
                $condition->integer();
            })
            ->withFloat(function($condition) {
                $condition->float();
            })
            ->withCarbonCarbon(function($condition) {
                $condition->carbon('Y-m-d H:i:s');
            })
            ->withCarbonDatetime(function($condition) {
                $condition->datetime();
            })
            ->withCarbonDate(function($condition) {
                $condition->date();
            })
            ->withCarbonTime(function($condition) {
                $condition->time();
            })
            ->withBoolean(function($condition) {
                $condition->boolean();
            })
            ->withTrue(function($condition) {
                $condition->true();
            })
            ->withFalse(function($condition) {
                $condition->false();
            })
            ->withIn(function($condition) {
                $condition->in(['a','b','c']);
            })
            ->withLength(function($condition) {
                $condition->length('>', 10);
            })
            ->withCompare(function($condition) {
                $condition->compare('===', 45, 'integer');
            })
            ->data([
                'required' => 'value',
                'isset' => '',
                'empty' => '',
                'uuid' => '3dd5dce2-f0f6-48a0-b0bc-eaedcbdea8de',
                'email' => 'valid.address@example.com',
                'string' => 'The Quick Brown Fox Jumped Over The Lazy Dog',
                'array' => [],
                'object' => (object) [],
                'numeric' => '45',
                'integer' => 45,
                'float' => 45.5,
                'carbon' => [
                    'carbon' => '2019-07-15 14:59:59',
                    'datetime' => '2018-11-15 18:29:29',
                    'date' => '2018-11-15',
                    'time' => '18:29:29'
                ],
                'boolean' => 'true',
                'true' => true,
                'false' => false,
                'in' => 'a',
                'length' => 'abcdefghijklmnopqrstuvwxyz',
                'compare' => 45
            ])
            ->onFail(function($classPoignant2) {
                return [$classPoignant2, $classPoignant2->errors];
            })
            ->onPass(function($classPoignant2) {
                return [$classPoignant2, true];
            })
            ->validate();

//        echo PHP_EOL . json_encode($results) . PHP_EOL;
//        echo PHP_EOL . md5(json_encode($results)) . PHP_EOL;

        $this->assertTrue(
            $classPoignant2->hasPassed()
            && 'b326b5062b2f0e69046810717534cb09' === md5(json_encode($results))
        );
        unset($classPoignant2);
    }
}