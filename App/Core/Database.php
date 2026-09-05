<?php

namespace App\Core;

use PDO;
use App\Core\Config;

class Database extends PDO
{
  use \App\Traits\LogToFile;

  private $DB_HOST = null;
  private $DB_PORT = null;
  private $DB_NAME = null;
  private $DB_USER = null;
  private $DB_PASSWORD = null;
  private $DB_CHARSET = null;

  private $conn;

  public function __construct($storage = 'Default')
  {
    // mysql //
    $this->DB_HOST = Config::$DB_STORAGE[$storage]['DB_HOST'];
    $this->DB_PORT = Config::$DB_STORAGE[$storage]['DB_PORT'];
    $this->DB_NAME = Config::$DB_STORAGE[$storage]['DB_DATABASE'];
    $this->DB_USER = Config::$DB_STORAGE[$storage]['DB_USERNAME'];
    $this->DB_PASSWORD = Config::$DB_STORAGE[$storage]['DB_PASSWORD'];
    $this->DB_CHARSET = Config::$DB_STORAGE[$storage]['DB_CHARSET'];

    try {
      $dsn = "mysql:host={$this->DB_HOST};dbname={$this->DB_NAME};port={$this->DB_PORT};charset={$this->DB_CHARSET}";
      $this->conn = new PDO($dsn, $this->DB_USER, $this->DB_PASSWORD);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } catch (\Throwable $th) {
      echo '<br>Ocorreu um erro ao consultar a base de dados.<br>';
      $logData = array('code' => $th->error_log, 'description' => $th->getMessage());
      self::setLog(json_encode($logData), 'error', 'DB');
      header('location: /unavailable.php');
    }
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
      $logData = array('origin' => $origin, 'query' => $query, 'parameters' => $parameters);
      self::setLog(json_encode($logData), 'monitoring', 'DB');
    }

    $th = null;
    try {
      $stmt = $this->conn->prepare($query);
      $this->mountQuery($stmt, $parameters);
      $stmt->execute();
    } catch (\Throwable $th) {
      /**
       * Gerar Log Erros
       */
      if ((int) $stmt->errorCode() > 0) {
        $logData = array('origin' => $origin, 'code' => $stmt->errorCode(), 'description' => $stmt->errorInfo(), 'throw' => $th->getMessage());
        self::setLog(json_encode($logData), 'error', 'DB');
      }
    }


    return $stmt;
  }
}
