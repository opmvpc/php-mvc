<?php

namespace Framework\Database\Migrations;

abstract class AbstractMigration
{
    /**
     * Sql query to create table.
     */
    abstract public function createTable(): void;
}
