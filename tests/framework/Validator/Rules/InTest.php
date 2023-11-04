<?php

use Framework\Validator\Rules\In;

describe('In Rule', function () {
    it('fails with a non-array', function () {
        $rule = new In(['foo', 'bar']);

        expect($rule->validate(['foo' => ''], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => null], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 1], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => 0], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => new stdClass()], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => ['foo', 'bar']], 'foo'))->toBeFalse();
    });

    it('passes with an array', function () {
        $rule = new In(['foo', 'bar']);

        expect($rule->validate(['foo' => 'foo'], 'foo'))->toBeTrue();
        expect($rule->validate(['foo' => 'bar'], 'foo'))->toBeTrue();
    });

    it('doesn\'t pass with an array with invalid values', function () {
        $rule = new In(['foo', 'bar']);

        expect($rule->validate(['foo' => 'baz'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => ['baz', 'qux']], 'foo'))->toBeFalse();
    });
});
