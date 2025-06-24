<?php
namespace App\Core\Classes;
use App\Core\Classes\Database;
require_once 'Database.php';

abstract class Model
{
  protected ?string $id;
  protected ?string $idColumn;
  protected ?Database $db;
  function __construct(protected string $modelName, bool $generateId = false, int $idLen = 5, string $idType = "alphanumerical")
  {
    $this->id = $generateId ? $this->generateId($idLen, $idType) : null;
    $this->db = Database::getInstance();
    $this->db->setTable($this->modelName);
  }
  public function getModelName():string
  {
    return $this->modelName;
  }
  public function register(array $data):bool
  {
    return $this->db->create($data);
  }
  public function findId(array $fields, string $id):array
  {
    return $this->select($fields, "$this->idColumn = '$id'")[0];
  }
  public function select(array $fields, string $where = '1=1', string $orderBy = '',
                        string $orderType = 'ASC', int $limit = 100):array
  {
    return $this->db->read($fields, $where, $orderBy, $orderType, $limit);
  }

  public function update(array $data, string $where):bool
  {
    return $this->db->update($data, $where);
  }

  public function delete(array $on)
  {
    return $this->db->delete($on);
  }
  public function deleteId(string $id)
  {
    return $this->db->delete([$this->idColumn=>$id]);
  }
  protected function generateId(int $len, string $type = 'alphanumeric'):string
  {
    $generated = "";
    $charSets = [
      'letters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
      'numbers' => '0123456789',
      'alphanumeric' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
      'alphanumericspecial' => 'abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ0123456789!@#$%^&*()_?',
      'ilegal' => '+-=[]{}|;:",.<>/~`'
    ];
    $activeCharSet = $charSets[$type] ?? $charSets['alphanumeric'];
    $charSetLength = strlen($activeCharSet);
    for ($i=0; $i < $len; $i++) {
      $generated .= $activeCharSet[random_int(0, $charSetLength - 1)];
    }
    return $generated;
  }
  protected function validChars(string $text, bool $validateLen = false, int $maxLen = 10, int $minLen = 1)
  {
    $ilegalChars = '+-=[]{}|;:",.<>/~`';
    $textLen = strlen($text);
    if($validateLen){
      if($textLen < $minLen || $textLen > $maxLen){
        return false;
      }
    }
    if(strpbrk($text,$ilegalChars) !== false){
      return false;
    }
    return true;
  }
  public function hashingWord(string $word):array
  {
    $validWord = $this->validChars($word, true, 55, 8);
    $hashed = $validWord ? password_hash($word, PASSWORD_DEFAULT) : "invalid";
    $status = $validWord ? "Hashed" : "Invalid";
    return [
      "Status"=>$status,
      "Hash"=>$hashed
    ];
  }
}

?>