<?php

namespace Framework\Validator\Rules;

use Framework\Support\Str;

class In implements RuleInterface
{
    /**
     * @param array<string> $elements
     */
    public function __construct(private array $elements) {}

    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        if (isset($data[$field])) {
            return in_array($data[$field], $this->elements);
        }

        return false;
    }

    public function message(array $data, string $field): string
    {
        $template = Str::translate('validation.in');

        $translatedField = Str::translate("fields.{$field}");
        $values = implode(', ', $this->elements);

        return str_replace([':field', ':values'], [$translatedField, $values], $template);
    }
}
