<?php

describe('Max Rule', function () {
    it('Should return true if the value is less than or equal to the max value', function () {
        $rule = new Framework\Validator\Rules\Max(18);
        expect($rule->validate(['age' => 18], 'age'))->toBeTrue();
        expect($rule->validate(['age' => 17], 'age'))->toBeTrue();
    });

    it('Should return false if the value is greater than the max value', function () {
        $rule = new Framework\Validator\Rules\Max(18);
        expect($rule->validate(['age' => 19], 'age'))->toBeFalse();
        expect($rule->validate(['age' => 20], 'age'))->toBeFalse();
    });

    it('Should return true if the string size is less than or equal to the max value', function () {
        $rule = new Framework\Validator\Rules\Max(5);
        expect($rule->validate(['name' => 'John'], 'name'))->toBeTrue();
        expect($rule->validate(['name' => 'Jane'], 'name'))->toBeTrue();
    });

    it('Should return false if the string size is greater than the max value', function () {
        $rule = new Framework\Validator\Rules\Max(5);
        expect($rule->validate(['name' => 'John Doe'], 'name'))->toBeFalse();
        expect($rule->validate(['name' => 'Jane Doe'], 'name'))->toBeFalse();
    });

    it('Should return true if the array size is less than or equal to the max value', function () {
        $rule = new Framework\Validator\Rules\Max(3);
        expect($rule->validate(['name' => [1, 2, 3]], 'name'))->toBeTrue();
        expect($rule->validate(['name' => [1, 2]], 'name'))->toBeTrue();
    });

    it('Should return false if the array size is greater than the max value', function () {
        $rule = new Framework\Validator\Rules\Max(3);
        expect($rule->validate(['name' => [1, 2, 3, 4]], 'name'))->toBeFalse();
        expect($rule->validate(['name' => [1, 2, 3, 4, 5]], 'name'))->toBeFalse();
    });
});
