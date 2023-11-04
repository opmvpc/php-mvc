<?php

use Framework\Validator\Rules\Boolean;

describe('Boolean Rule', function () {
    it('fails with a non-boolean', function () {
        $rule = new Boolean();

        expect($rule->validate(['foo' => 'bar'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => ''], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => null], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => []], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => new stdClass()], 'foo'))->toBeFalse();
    });

    it('passes with a boolean', function () {
        $rule = new Boolean();

        expect($rule->validate(['foo' => true], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => false], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 1], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => 0], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => '1'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => '0'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 'true'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => 'false'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 'on'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => 'off'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 'yes'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => 'no'], 'foo'))->toBeFalse();
    });
});
