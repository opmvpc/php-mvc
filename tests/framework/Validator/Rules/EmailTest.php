<?php

use Framework\Validator\Rules\Email;

describe('Email Rule', function () {
    it('fails with a non-email', function () {
        $rule = new Email();

        expect($rule->validate(['foo' => 'bar'], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => ''], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => null], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => []], 'foo'))->toBeFalse();
        expect($rule->validate(['foo' => new stdClass()], 'foo'))->toBeFalse();
    });

    it('passes with an email', function () {
        $rule = new Email();

        expect($rule->validate(['foo' => 'test@example.com'], 'foo'))->toBeTrue();
    });

    it('doesn\'t pass with an email with invalid domain', function () {
        $rule = new Email();

        expect($rule->validate(['foo' => 'test@example'], 'foo'))->toBeFalse();
    });
});
