<?php

namespace App\Core;

use PDO;
use App\Core\Config;
use RuntimeException;

class Database extends PDO
{
  use \App\Traits\LogToFile;

  private $DB_HOST = null;
  private $DB_PORT = null;
  private $DB_NAME = null;
  private $DB_USER = null;
  private $DB_PASSWORD = null;
  private $DB_CHARSET = null;
  private string $storage;

  private $conn;

  public function __construct($storage = 'Default')
  {
    $this->storage = $storage;

    try {
      $config = Config::getDbStorage($storage);
      $this->DB_HOST = $config['DB_HOST'];
      $this->DB_PORT = $config['DB_PORT'];
      $this->DB_NAME = $config['DB_DATABASE'];
      $this->DB_USER = $config['DB_USERNAME'];
      $this->DB_PASSWORD = $config['DB_PASSWORD'];
      $this->DB_CHARSET = $config['DB_CHARSET'];

      $dsn = "mysql:host={$this->DB_HOST};dbname={$this->DB_NAME};port={$this->DB_PORT};charset={$this->DB_CHARSET}";
      $this->conn = new PDO($dsn, $this->DB_USER, $this->DB_PASSWORD);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } catch (\Throwable $th) {
      $logData = array('storage' => $this->storage, 'description' => $th->getMessage());
      self::setLog(json_encode($logData), 'error', 'DB');

      if (PHP_SAPI === 'cli') {
        throw new RuntimeException("Ocorreu um erro ao consultar o storage {$this->storage}.", 0, $th);
      }

      echo '<br>Ocorreu um erro ao consultar a base de dados.<br>';
      header('location: /unavailable.php');
      exit();
    }
  }

  public function getStorage(): string
  {
    return $this->storage;
  }

  public function getDatabaseName(): string
  {
    return (string) $this->DB_NAME;
  }

  public function getPdo(): PDO
  {
    return $this->conn;
  }

  private function setParameters($stmt, $key, $value)
  {
    $stmt->bindParam($key, $value);
  }

  private function mountQuery($stmt, $parameters)
  {
    foreach ($parameters as $key => $value) {
      $this->setParameters($stmt, $key, $value);
    }
  }

  public function executeQuery(string $origin, string $query, array $parameters = [])
  {
    /**
     * Gerar Log Queries
     */
    if (Config::$MONITORING_QUERY === true) {
      $logData = array('storage' => $this->storage, 'origin' => $origin, 'query' => $query, 'parameters' => $parameters);
      self::setLog(json_encode($logData), 'monitoring', 'DB');
    }

    $th = null;
    $stmt = null;
    try {
      $stmt = $this->conn->prepare($query);
      $this->mountQuery($stmt, $parameters);
      $stmt->execute();
    } catch (\Throwable $th) {
      /**
       * Gerar Log Erros
       */
      if ($stmt !== null && (int) $stmt->errorCode() > 0) {
        $logData = array('storage' => $this->storage, 'origin' => $origin, 'code' => $stmt->errorCode(), 'description' => $stmt->errorInfo(), 'throw' => $th->getMessage());
        self::setLog(json_encode($logData), 'error', 'DB');
      } else {
        $logData = array('storage' => $this->storage, 'origin' => $origin, 'throw' => $th->getMessage());
        self::setLog(json_encode($logData), 'error', 'DB');
      }

      if (PHP_SAPI === 'cli') {
        throw $th;
      }
    }


    return $stmt;
  }
}
