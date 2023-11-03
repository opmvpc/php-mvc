<?php

declare(strict_types=1);

namespace Framework\Validator\Rules;

use Framework\Database\DB;
use Framework\Support\Str;

class Unique implements RuleInterface
{
    public function __construct(private string $table, private null|int $id = null) {}

    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data, string $field): bool
    {
        $res = DB::query(
            <<<SQL
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE {$field} = :{$field}
            SQL,
            [$field => $data[$field]]
        );

        $count = $res->fetchColumn();

        if (null !== $this->id) {
            $res = DB::query(
                <<<SQL
                SELECT COUNT(*)
                FROM {$this->table}
                WHERE id = :id
                SQL,
                ['id' => $this->id]
            );

            $count = $res->fetchColumn() ? 0 : $count;
        }

        return 0 === $count;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function message(array $data, string $field): string
    {
        $template = Str::translate('validation.unique');

        $translatedField = Str::translate("fields.{$field}");

        return str_replace(':field', $translatedField, $template);
    }
}
