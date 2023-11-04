<?php

use Framework\Validator\Rules\Required;

describe('Required Rule', function () {
    it('Should return true if the value is not empty', function () {
        $rule = new Required();
        expect($rule->validate(['name' => 'John Doe'], 'name'))->toBeTrue();
        expect($rule->validate(['name' => 1], 'name'))->toBeTrue();
        expect($rule->validate(['name' => true], 'name'))->toBeTrue();
        expect($rule->validate(['name' => [1, 2, 3]], 'name'))->toBeTrue();
    });

    it('Should return false if the value is empty', function () {
        $rule = new Required();
        expect($rule->validate(['name' => ''], 'name'))->toBeFalse();
        expect($rule->validate(['name' => null], 'name'))->toBeFalse();
    });
});
