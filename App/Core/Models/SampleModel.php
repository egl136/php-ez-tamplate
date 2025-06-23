<?php 
namespace App\Core\Models;
use App\Core\Classes\Model;

require_once __DIR__ . '/../Classes/Model.php';

class SampleModel extends Model
{
	function __construct(protected String $model_name = "sample")
	{

	}
}
