<?php

declare(strict_types=1);

namespace Framework\Validator\Rules;

use Framework\Support\Str;

class Boolean implements RuleInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        if (isset($data[$field])) {
            return false !== filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string
    {
        $template = Str::translate('validation.boolean');

        $translatedField = Str::translate("fields.{$field}");

        return str_replace(':field', $translatedField, $template);
    }
}
