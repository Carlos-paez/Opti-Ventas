<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

final class Database
{
    private static ?PDO $pdo = null;

    private static int $transactionDepth = 0;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $host = (string) config('db.host', '127.0.0.1');
            $port = (int) config('db.port', 3306);
            $name = (string) config('db.database');
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            self::$pdo = new PDO($dsn, (string) config('db.username', 'root'), (string) config('db.password'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$pdo;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $value = self::run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }

    public static function insert(string $sql, array $params = []): int
    {
        self::run($sql, $params);

        return (int) self::connection()->lastInsertId();
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();
        $nested = $pdo->inTransaction();
        self::$transactionDepth++;

        if ($nested) {
            $pdo->exec('SAVEPOINT spi' . self::$transactionDepth);
        } else {
            $pdo->beginTransaction();
        }

        try {
            $result = $callback();
            if (!$nested) {
                $pdo->commit();
            }
            self::$transactionDepth--;

            return $result;
        } catch (\Throwable $e) {
            if ($nested) {
                $pdo->exec('ROLLBACK TO SAVEPOINT spi' . self::$transactionDepth);
            } elseif ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            self::$transactionDepth--;

            throw $e;
        }
    }
}