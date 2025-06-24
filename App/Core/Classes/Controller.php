<?php 
namespace App\Core\Classes;

class Controller
{
	protected Array $values;
	protected Model $model;
	
	public $message;
	function __construct()
	{
		
	}
	protected function setModel(Model $model):void
	{
		$this->model = $model;
	}
	public function select(String $where = "1=1", String $order_by = "", String $order_type = "ASC",  String $limit = "100"):Array
	{
		$this->model->set_fields($this->fields);
		return $this->model->retrieve_all($where,$order_by,$order_type,$limit);
	}
	public function selectRow(array $fields, String $where): array
	{
		$row = $this->model->select($fields, $where);
		$result = count($row) == 0 ? $row[0] : [];
		return $result;
	}
	public function save(array $fields):bool
	{
		$this->model->set_fields($this->fields);
		if($this->verify_posts()){			
			$this->model->set_values($this->values);
			return $this->model->register();
		}

		return false;
	}

	public function setModelFields(Array $fields):void
	{
		$this->fields = $fields;
	}
	
	public function setModelValues(Array $values):void
	{
		$this->values = $values;
	}

}
?>
