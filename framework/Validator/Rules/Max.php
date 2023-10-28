<?php

declare(strict_types=1);

namespace Framework\Validator\Rules;

use Framework\Support\Str;

class Max implements RuleInterface
{
    public function __construct(private int $max) {}

    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        if (is_numeric($data[$field])) {
            return $data[$field] <= $this->max;
        }
        if (is_string($data[$field])) {
            return strlen($data[$field]) <= $this->max;
        }
        if (is_array($data[$field])) {
            return count($data[$field]) <= $this->max;
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string
    {
        $templateName = '';
        if (is_numeric($data[$field])) {
            $templateName = 'validation.min.numeric';
        }
        if (is_string($data[$field])) {
            $templateName = 'validation.min.string';
        }
        if (is_array($data[$field])) {
            $templateName = 'validation.min.array';
        }

        $template = Str::translate($templateName);

        $translatedField = Str::translate("fields.{$field}");

        return str_replace([':field', ':max'], [$translatedField, $this->max], $template);
    }
}
