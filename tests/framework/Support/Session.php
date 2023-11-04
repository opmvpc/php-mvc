<?php

use Framework\Support\Session;

describe('Session tests', function () {
    beforeEach(function () {
        $_SESSION = [];
        $this->session = new Session();
    });

    afterEach(function () {
        unset($_SESSION);
    });

    it('should start a session', function () {
        $this->session->start();
        expect($this->session->get('_csrf_token'))->toBeString();
        expect($this->session->get('_old_csrf_token'))->toBeNull();
    });

    it('should stop a session', function () {
        $this->session->start();
        $this->session->stop();
        expect($this->session->get('_csrf_token'))->toBeNull();
        expect($this->session->get('_old_csrf_token'))->toBeNull();
    });

    it('should get a session value', function () {
        $this->session->start();
        $this->session->set('name', 'John');
        expect($this->session->get('name'))->toBe('John');
    });

    it('should get a default value if the session value does not exist', function () {
        $this->session->start();
        expect($this->session->get('name', 'John'))->toBe('John');
    });

    it('should set a session value', function () {
        $this->session->start();
        $this->session->set('name', 'John');
        expect($this->session->get('name'))->toBe('John');
    });

    it('should delete a session value', function () {
        $this->session->start();
        $this->session->set('name', 'John');
        expect($this->session->get('name'))->toBe('John');
        $this->session->delete('name');
        expect($this->session->get('name'))->toBeNull();
    });

    it('should destroy a session', function () {
        $this->session->start();
        $this->session->set('name', 'John');
        expect($this->session->get('name'))->toBe('John');
        $this->session->destroy();
        expect($this->session->get('name'))->toBeNull();
    });

    it('should flash a session value', function () {
        $this->session->start();
        $this->session->flash('name', 'John');
        expect($this->session->get('name'))->toBe('John');
        expect($this->session->get('_flash'))->toBeArray();
        expect($this->session->get('_flash')[Session::id()])->toBeArray();
        expect($this->session->get('_flash')[Session::id()]['name'])->toBe('John');
    });

    it('should check if a session value exists', function () {
        $this->session->start();
        $this->session->set('name', 'John');
        expect($this->session->has('name'))->toBeTrue();
        expect($this->session->has('age'))->toBeFalse();
    });

    it('should check if a session value exists in the flash', function () {
        $this->session->start();
        $this->session->flash('name', 'John');
        expect($this->session->has('name'))->toBeTrue();
        expect($this->session->has('age'))->toBeFalse();
    });

    it('should destroy the flash', function () {
        $this->session->start();
        $this->session->flash('name', 'John');
        expect($this->session->has('name'))->toBeTrue();
        expect($this->session->has('age'))->toBeFalse();
        $this->session->destroyFlash();
        expect($this->session->has('name'))->toBeFalse();
        expect($this->session->has('age'))->toBeFalse();
    });

    it('should return the session id', function () {
        $this->session->start();
        expect($this->session->id())->toBeString();
    });
});
