<?php

declare(strict_types=1);

namespace Framework\Validator;

use Framework\Exceptions\ValidationException;
use Framework\Validator\Rules\RuleInterface;

class Validator
{
    /**
     * @var array<string, list<RuleInterface>>
     */
    private array $rules;

    /**
     * @param array<string, list<RuleInterface>> $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return array<string, list<string>>
     */
    public function validate(mixed $data): array
    {
        if (!\is_array($data)) {
            throw new \InvalidArgumentException('Data must be an array');
        }

        $errors = [];

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                if (!\array_key_exists($field, $data)) {
                    throw new \InvalidArgumentException("Field {$field} does not exist in data");
                }

                if (!$rule->validate($data, $field)) {
                    $errors[$field][] = $rule->message($data, $field);
                }
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        return \array_intersect_key($data, $errors);
    }
}
