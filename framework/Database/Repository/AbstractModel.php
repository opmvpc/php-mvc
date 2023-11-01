<?php

declare(strict_types=1);

namespace Framework\Database\Repository;

use Framework\Database\DB;

abstract class AbstractModel
{
    protected DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }
}
