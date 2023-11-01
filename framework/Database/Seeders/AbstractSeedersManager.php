<?php

namespace Framework\Database\Seeders;

abstract class AbstractSeedersManager
{
    /**
     * @var AbstractSeeder[]
     */
    protected array $seeders = [];

    abstract public function register(): void;

    public function seed(): void
    {
        $this->register();

        foreach ($this->seeders as $seeder) {
            $seeder->run();
        }
    }
}
