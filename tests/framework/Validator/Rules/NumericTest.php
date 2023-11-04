<?php

use Framework\Validator\Rules\Numeric;

describe('Numeric Rule', function () {
    it('Should return true if the value is numeric', function () {
        $rule = new Numeric();
        expect($rule->validate(['age' => 18], 'age'))->toBeTrue();
        expect($rule->validate(['age' => 18.5], 'age'))->toBeTrue();
        expect($rule->validate(['age' => '18'], 'age'))->toBeTrue();
    });

    it('Should return false if the value is not numeric', function () {
        $rule = new Numeric();
        expect($rule->validate(['age' => '18a'], 'age'))->toBeFalse();
        expect($rule->validate(['age' => ''], 'age'))->toBeFalse();
        expect($rule->validate(['age' => null], 'age'))->toBeFalse();
        expect($rule->validate(['age' => []], 'age'))->toBeFalse();
        expect($rule->validate(['age' => true], 'age'))->toBeFalse();
    });
});
