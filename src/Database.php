<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

require_once dirname(__DIR__) . '/config.php';

final class Database
{
    private string $username;
    private string $password;
    private string $host;
    private int $port;
    private string $database;

    public function __construct()
    {
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->host = DB_HOST;
        $this->port = DB_PORT;
        $this->database = DB_NAME;
    }

    public function connect(): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;sslmode=prefer',
            $this->host,
            $this->port,
            $this->database,
        );

        try {
            return new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Nie udało się połączyć z bazą danych.',
                0,
                $exception,
            );
        }
    }
}
