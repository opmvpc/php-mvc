<?php

use Framework\Exceptions\ValidationException;
use Framework\Validator\Rules\Max;
use Framework\Validator\Rules\Min;
use Framework\Validator\Rules\Required;
use Framework\Validator\Validator;

describe('Validator tests', function () {
    it('can validate a string', function () {
        $validator = new Validator([
            'name' => [
                new Min(3),
                new Max(10),
                new Required(),
            ],
        ]);

        $validated = $validator->validate([
            'name' => 'John',
        ]);

        expect($validated)->toBeArray();
        expect($validated['name'])->toBe('John');
    });

    it('should throw an exception if data is invalid', function () {
        $validator = new Validator([
            'name' => [
                new Min(3),
                new Max(10),
                new Required(),
            ],
        ]);

        $input = [
            'name' => 'Jo',
        ];
        expect(fn () => $validator->validate($input))->toThrow(ValidationException::class);
    });

    it('should throw an exception if data is not an array', function () {
        $validator = new Validator([
            'name' => [
                new Min(3),
                new Max(10),
                new Required(),
            ],
        ]);

        $input = 'John';
        expect(fn () => $validator->validate($input))->toThrow(\InvalidArgumentException::class);
    });

    it('should throw an exception if field does not exist in data', function () {
        $validator = new Validator([
            'name' => [
                new Min(3),
                new Max(10),
                new Required(),
            ],
        ]);

        $input = [
            'age' => 30,
        ];
        expect(fn () => $validator->validate($input))->toThrow(\InvalidArgumentException::class);
    });
});
