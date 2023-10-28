<?php

namespace Framework\Validator\Rules;

use Framework\Support\Str;

class Numeric implements RuleInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        if (isset($data[$field])) {
            return is_numeric($data[$field]);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string
    {
        $template = Str::translate('validation.numeric');

        $translatedField = Str::translate("fields.{$field}");

        return str_replace(':field', $translatedField, $template);
    }
}
