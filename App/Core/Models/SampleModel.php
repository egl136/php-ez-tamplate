<?php 
namespace App\Core\Models;
use App\Core\Classes\Model;

require_once __DIR__ . '/../Classes/Model.php';

class SampleModel extends Model
{
	function __construct(protected String $model_name = "sample", bool $generateId = false, int $idLen = 5, string $idType = "alphanumerical")
	{
		$this->idColumn = "id";
		$this->model_name = $model_name;
		parent::__construct($model_name);
	}

}
