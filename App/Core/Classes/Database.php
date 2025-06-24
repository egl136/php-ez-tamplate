<?php
namespace App\Core\Classes;
use PDO;
final class Database
{
  private static ?Database $instance = null;
  private string $table;
  private array $fields;
  private PDO $connection;
  private string $dbManager;
  private string $dbDatabse;
  private string $dbHost;
  private string $dbCharset;
  private string $dbPort;
  private string $dbUser;
  private string $dbPassword;
  private function __construct()
  {
    $this->dbManager  = config('database.driver');
    $this->dbName = config('database.name');
    $this->dbHost     = config('database.host');
    $this->dbCharset  = config('database.charset');
    $this->dbUser     = config('database.user');
    $this->dbPassword = config('database.pass');
    $this->dbPort = config('database.port');
    
    $this->attemptConnection();
  }
  public static function getInstance(): Database {
    if (self::$instance === null) {
      self::$instance = new Database();
    }
    return self::$instance;
  }
  public function setTable(string $table){
    $this->table = $table;
  }
  

  public function create(array $fetched_data):bool
  {
    $fields = array_keys($fetched_data);
    //Transforma las keys del arreglo asociativo a un string
    $fieldsString = implode(',',$fields);
    //Concatena ":" a cada key del arreglo para preparar el query
    $prepareFields = array_map(function($field) {
      return ":" . $field;
    }, $fields);
    $prepareFieldsString = implode(',',$prepareFields);
    $insertQuery = "INSERT INTO $this->table($fieldsString) VALUES($prepareFieldsString)";
    $stmt = $this->connection->prepare($insertQuery);

    $creation = $stmt->execute($fetched_data);
    //Mostrar query
    //Solo activar si es necesario.
    //CAUSA CONFLICTO CUANDO SE ENVIA RESPONSE SI SE ACCEDE A LA API
    //$msg = $creation  ? "Creado" : "No creado $insesrtQuery";
    //echo $msg;
    return $creation;

  }
  public function read(array $fields, string $where = '1=1', string $orderBy = '',
                        string $orderType = 'ASC', int $limit = 100):array
  {
    $fetchedResults = [];
    $orderClause = $orderBy ? "ORDER BY {$orderBy} {$orderType}" : '';
    $fieldsString = implode(",",$fields);
    $selectQuery = "SELECT $fieldsString FROM $this->table WHERE $where $orderClause";
    $stmt = $this->connection->prepare($selectQuery);
    $fetchedResults = $stmt->execute() ? $stmt->fetchAll() : [];
    return $fetchedResults;
  }
  public function update(array $data, string $where)
  {
    $setClause = implode(', ', array_map(fn($field) => "{$field} = ?", array_keys($data)));
    $values = array_values($data);
    $updateQuery = "UPDATE {$this->table} SET {$setClause} WHERE {$where}";
    $stmt = $this->connection->prepare($updateQuery);
    if ($stmt === false) {
      return false;
    }
    return $stmt->execute($values);
  }
  public function delete(array $on):bool
  {
    $whereClause = implode('AND ', array_map(fn($field) => "{$field} = ?", array_keys($on)));
    $where = array_values($on);
    $deleteQuery = "DELETE FROM $this->table WHERE $whereClause";
    $stmt = $this->connection->prepare($deleteQuery);
    if ($stmt === false) {
      return false;
    }
    return $stmt->execute($where);
  }
  //Connection
  //Usando ERRMODE_EXCEPTION para debug
  //borrar PDO::ATTR_ERRMODE cuando se lance a producción o establecerlo
  //en ERRMODE_SILENT
  private function attemptConnection():bool
  {
    try {
      $dsn = "$this->dbManager:host=$this->dbHost;dbname=$this->dbName;charset=$this->dbCharset";
      $this->connection = new PDO($dsn, $this->dbUser, $this->dbPassword, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false
      ]);
      return true;
    } catch (PDOException $e) {
      return false;
    }

  }

}
?>
