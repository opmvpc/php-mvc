<?php

use Framework\Validator\Rules\Integer;

describe('Integer Rule', function () {
    it('fails with a non-integer', function () {
        $rule = new Integer();

        expect($rule->validate(['foo' => 'bar'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => ''], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => null], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => []], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => new stdClass()], 'foo'))->toBeFalse();
    });

    it('passes with an integer', function () {
        $rule = new Integer();

        expect($rule->validate(['foo' => 1], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => 0], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => -1], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => '1'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => '0'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => '-1'], 'foo'))->toBeTrue();
    });

    it('doesn\'t pass with a float', function () {
        $rule = new Integer();

        expect($rule->validate(['foo' => 1.1], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 0.1], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => -1.1], 'foo'))->toBeFalse();
    });
});
