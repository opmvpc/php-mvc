<?php

declare(strict_types=1);

namespace Framework\Validator\Rules;

use Framework\Support\Str;

class Url implements RuleInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        if (isset($data[$field])) {
            return false !== filter_var($data[$field], FILTER_VALIDATE_URL);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string
    {
        $template = Str::translate('validation.url');

        $translatedField = Str::translate("fields.{$field}");

        return str_replace(':field', $translatedField, $template);
    }
}
