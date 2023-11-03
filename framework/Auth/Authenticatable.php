<?php

declare(strict_types=1);

namespace Framework\Auth;

use Framework\Database\DB;
use Framework\Database\Repository\AbstractModel;

class Authenticatable extends AbstractModel
{
    public function __construct(
        private null|int $id,
        private string $name,
        private string $email,
        private string $password,
    ) {}

    public function id(): null|int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function setName(string $name): Authenticatable
    {
        $this->name = $name;

        return $this;
    }

    public function setEmail(string $email): Authenticatable
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword(string $password): Authenticatable
    {
        $this->password = $password;

        return $this;
    }

    public static function findOrFail(mixed $id): Authenticatable
    {
        if (null === $id) {
            throw new \Exception('User id is required');
        }

        if (\is_string($id)) {
            $id = (int) $id;
        }

        if (!\is_int($id)) {
            throw new \Exception('User id must be an integer');
        }

        $user = DB::query(
            <<<'SQL'
            SELECT id, name, email, password
            FROM users
            WHERE id = :id
            SQL,
            ['id' => $id]
        )->fetch();

        if (null !== $user) {
            if (!\is_array($user)) {
                throw new \Exception('User must be an array');
            }

            return self::fromRow($user);
        }

        throw new \Exception('User not found');
    }

    public static function findByEmail(mixed $email): Authenticatable
    {
        if (null === $email) {
            throw new \Exception('User email is required');
        }

        if (!\is_string($email)) {
            throw new \Exception('User email must be a string');
        }

        $user = DB::query(
            <<<'SQL'
            SELECT id, name, email, password
            FROM users
            WHERE email = :email
            SQL,
            ['email' => $email]
        )->fetch();

        if (null !== $user) {
            if (!\is_array($user)) {
                throw new \Exception('User must be an array');
            }

            return self::fromRow($user);
        }

        throw new \Exception('User not found');
    }

    /**
     * @return array<Authenticatable>
     */
    public static function all(): array
    {
        $users = DB::query(
            <<<'SQL'
            SELECT id, name, email, password
            FROM users
            SQL
        )->fetchAll();

        return \array_map(
            fn (array $user): Authenticatable => self::fromRow($user),
            $users
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    public function save(): Authenticatable
    {
        if (null === $this->id) {
            $this->id = $this->insert();
        } else {
            $this->update();
        }

        return $this;
    }

    public function delete(): void
    {
        if (null === $this->id) {
            throw new \Exception('User id is required');
        }

        DB::query(
            <<<'SQL'
            DELETE FROM users
            WHERE id = :id
            SQL,
            ['id' => $this->id]
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function fromRow(array $row): Authenticatable
    {
        if (!\is_int($row['id'])) {
            throw new \Exception('User id must be an integer');
        }

        if (!\is_string($row['name'])) {
            throw new \Exception('User name must be a string');
        }

        if (!\is_string($row['email'])) {
            throw new \Exception('User email must be a string');
        }

        if (!\is_string($row['password'])) {
            throw new \Exception('User password must be a string');
        }

        return new Authenticatable(
            $row['id'],
            $row['name'],
            $row['email'],
            $row['password'],
        );
    }

    private function insert(): int
    {
        DB::query(
            <<<'SQL'
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
            SQL,
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]
        );

        return DB::lastInsertId();
    }

    private function update(): void
    {
        DB::query(
            <<<'SQL'
            UPDATE users
            SET name = :name, email = :email, password = :password
            WHERE id = :id
            SQL,
            [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]
        );
    }
}
