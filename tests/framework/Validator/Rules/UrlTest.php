<?php

use Framework\Validator\Rules\Url;

describe('Url Rule', function () {
    it('Should return true if the value is a valid url', function () {
        $rule = new Url();
        expect($rule->validate(['url' => 'http://www.google.com'], 'url'))->toBeTrue();
        expect($rule->validate(['url' => 'https://www.google.com'], 'url'))->toBeTrue();
        expect($rule->validate(['url' => 'http://google.com'], 'url'))->toBeTrue();
        expect($rule->validate(['url' => 'https://google.com'], 'url'))->toBeTrue();
    });

    it('Should return false if the value is not a valid url', function () {
        $rule = new Url();
        expect($rule->validate(['url', 'http://www.google.com'], 'url'))->toBeFalse();
        expect($rule->validate(['url' => 'google.com'], 'url'))->toBeFalse();
    });
});
