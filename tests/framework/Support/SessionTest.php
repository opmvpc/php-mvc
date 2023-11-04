<?php

use Framework\Support\Session;

describe('Session tests', function () {
    it('should get a session value', function () {
        Session::set('name', 'John');
        expect(Session::get('name'))->toBe('John');
    });

    it('should get a default value if the session value does not exist', function () {
        expect(Session::get('name', 'John'))->toBe('John');
    });

    it('should set a session value', function () {
        Session::set('name', 'John');
        expect(Session::get('name'))->toBe('John');
    });

    it('should delete a session value', function () {
        Session::set('name', 'John');
        expect(Session::get('name'))->toBe('John');
        Session::delete('name');
        expect(Session::get('name'))->toBeNull();
    });

    it('should flash a session value', function () {
        Session::flash('name', 'John');
        expect(Session::get('name'))->toBe('John');
        expect(Session::get('_flash'))->toBeArray();
        expect(Session::get('_flash')[Session::id()])->toBeArray();
        expect(Session::get('_flash')[Session::id()]['name'])->toBe(true);
    });

    it('should check if a session value exists', function () {
        Session::set('name', 'John');
        expect(Session::has('name'))->toBeTrue();
        expect(Session::has('age'))->toBeFalse();
    });

    it('should check if a session value exists in the flash', function () {
        Session::flash('name', 'John');
        expect(Session::has('name'))->toBeTrue();
        expect(Session::has('age'))->toBeFalse();
    });

    it('should destroy the flash', function () {
        Session::flash('name', 'John');
        expect(Session::has('name'))->toBeTrue();
        expect(Session::has('age'))->toBeFalse();
    });

    it('should return the session id', function () {
        expect(Session::id())->toBeString();
    });
});
