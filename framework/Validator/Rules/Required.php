<?php

namespace Framework\Validator\Rules;

use Framework\Support\Str;

class Required implements RuleInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        return isset($data[$field]) && '' !== $data[$field];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string
    {
        $template = Str::translate('validation.required');

        // translate the field name
        $translatedField = Str::translate("fields.{$field}");

        return str_replace(':field', $translatedField, $template);
    }
}
