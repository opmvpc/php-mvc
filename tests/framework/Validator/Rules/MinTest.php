<?php

use Framework\Validator\Rules\Min;

describe('Min Rule', function () {
    it('Should return true if the value is greater than the min value', function () {
        $rule = new Min(18);
        expect($rule->validate(['age' => 19], 'age'))->toBeTrue();
        expect($rule->validate(['age' => 18.5], 'age'))->toBeTrue();
    });

    it('Should return true if the value is equal to the min value', function () {
        $rule = new Min(18);
        expect($rule->validate(['age' => 18], 'age'))->toBeTrue();
    });

    it('Should return false if the value is less than the min value', function () {
        $rule = new Min(18);
        expect($rule->validate(['age' => 17], 'age'))->toBeFalse();
        expect($rule->validate(['age' => 17.5], 'age'))->toBeFalse();
    });

    it('Should return true if the string size is greater than the min value', function () {
        $rule = new Min(5);
        expect($rule->validate(['name' => 'Johny'], 'name'))->toBeTrue();
        expect($rule->validate(['name' => 'John Doe'], 'name'))->toBeTrue();
        expect($rule->validate(['name' => 'Jane Doe'], 'name'))->toBeTrue();
    });

    it('Should return false if the string size is less than the min value', function () {
        $rule = new Min(5);
        expect($rule->validate(['name' => 'John'], 'name'))->toBeFalse();
        expect($rule->validate(['name' => 'Jane'], 'name'))->toBeFalse();
    });

    it('Should return true if the array size is greater than the min value', function () {
        $rule = new Min(3);

        expect($rule->validate(['name' => [1, 2, 3]], 'name'))->toBeTrue();
        expect($rule->validate(['name' => [1, 2, 3, 4]], 'name'))->toBeTrue();
        expect($rule->validate(['name' => [1, 2, 3, 4, 5]], 'name'))->toBeTrue();
    });

    it('Should return false if the array size is less than the min value', function () {
        $rule = new Min(3);
        expect($rule->validate(['name' => [1, 2]], 'name'))->toBeFalse();
        expect($rule->validate(['name' => [1]], 'name'))->toBeFalse();
    });
});
