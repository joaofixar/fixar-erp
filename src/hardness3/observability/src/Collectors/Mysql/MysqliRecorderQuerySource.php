<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Adaptador mysqli usado exclusivamente pelo comando de coleta em segundo plano. */
final class MysqliRecorderQuerySource implements RecorderQuerySource
{
    private $connection;

    private function __construct(\mysqli $connection)
    {
        $this->connection = $connection;
    }

    public static function connect(array $tenant): self
    {
        if (!extension_loaded('mysqli') || !function_exists('mysqli_init')) {
            throw new \RuntimeException('mysql_recorder_mysqli_extension_unavailable');
        }
        $database = isset($tenant['database']) && is_array($tenant['database']) ? $tenant['database'] : array();
        foreach (array('host', 'username', 'password') as $required) {
            if (!array_key_exists($required, $database)) {
                throw new \RuntimeException('mysql_recorder_connection_configuration_missing');
            }
        }

        $connection = mysqli_init();
        if (!$connection) {
            throw new \RuntimeException('mysql_recorder_initialization_failed');
        }
        if (defined('MYSQLI_OPT_CONNECT_TIMEOUT')) { $connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 2); }
        if (defined('MYSQLI_OPT_READ_TIMEOUT')) { $connection->options(MYSQLI_OPT_READ_TIMEOUT, 4); }
        $connected = @$connection->real_connect(
            (string) $database['host'],
            (string) $database['username'],
            (string) $database['password'],
            isset($database['database']) ? (string) $database['database'] : 'mysql',
            isset($database['port']) ? (int) $database['port'] : 3306
        );
        if (!$connected) {
            $connection->close();
            throw new \RuntimeException('mysql_recorder_connection_failed');
        }
        if (!$connection->set_charset('utf8mb4')) {
            $connection->close();
            throw new \RuntimeException('mysql_recorder_charset_failed');
        }
        return new self($connection);
    }

    public function rows(string $sql, array $parameters = array()): array
    {
        if ($parameters === array()) {
            $result = @$this->connection->query($sql);
            if (!$result instanceof \mysqli_result) {
                throw new \RuntimeException('mysql_recorder_query_failed: ' . $this->connection->error);
            }
            return $this->fetch($result);
        }

        $statement = @$this->connection->prepare($sql);
        if (!$statement instanceof \mysqli_stmt) {
            throw new \RuntimeException('mysql_recorder_prepare_failed: ' . $this->connection->error);
        }
        try {
            $types = str_repeat('s', count($parameters));
            $values = array_values(array_map('strval', $parameters));
            $references = array($types);
            foreach ($values as $index => $value) { $references[] = &$values[$index]; }
            if (!call_user_func_array(array($statement, 'bind_param'), $references) || !$statement->execute()) {
                throw new \RuntimeException('mysql_recorder_query_failed: ' . $statement->error);
            }
            $result = $statement->get_result();
            if (!$result instanceof \mysqli_result) {
                throw new \RuntimeException('mysql_recorder_result_unavailable');
            }
            return $this->fetch($result);
        } finally {
            $statement->close();
        }
    }

    public function close(): void
    {
        $this->connection->close();
    }

    private function fetch(\mysqli_result $result): array
    {
        $rows = array();
        while ($row = $result->fetch_assoc()) { $rows[] = $row; }
        $result->free();
        return $rows;
    }
}
