<?php

namespace Framework\Validator\Rules;

interface RuleInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string;
}
