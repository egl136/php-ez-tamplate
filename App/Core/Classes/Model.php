<?php
namespace App\Core\Classes;

class Model
{
	protected ?Array $fields;
	public ?Array $values;
	public String $msg;
	function __construct(protected String $model_name)
	{
		
	}
	public function get_fields():Array
	{
		return $this->fields;
	}
	public function set_fields(Array $fields):void
	{
		$this->fields = $fields;
	}
	public function set_values(Array $values):void
	{
		$this->values = $values;
	}


	public function build_model():void
	{
		DATABASE->select_table($this->model_name);
		DATABASE->set_fields($this->fields);
	}
	private function destroy_model():void
	{
		DATABASE->select_table("");
		DATABASE->set_fields([]);
	}


	public function register():bool
	{
		$this->build_model();
		$registered = $this->save_on_database();
		$this->msg = "ola";
		$this->destroy_model();
		return $registered;
	}
	public function retrieve_all(String $where = "1=1", String $order_by = "", String $order_type = "ASC",  String $limit = "100"):Array
	{
		$this->build_model();
		$data = $this->get_from_table($where,$order_by,$order_type,$limit);
		$this->destroy_model();
		return $data;
	}
	public function retrieve_one($where):Array
	{
		$this->build_model();
		$data = $this->get_one_from_table($where);
		$this->destroy_model();
		return $data;
	}
	private function save_on_database():bool
	{
		
		$saved = DATABASE->create($this->values);
		
		return $saved;
	}
	private function get_from_table(String $where = "1=1", String $order_by = "", String $order_type = "ASC",  String $limit = "100"):Array
	{
		
		DATABASE->select_from($where,$order_by,$order_type,$limit);
		return DATABASE->results;
	}
	private function get_one_from_table(String $where):Array
	{
		return DATABASE->select_one($where);
	}
	public function update(String $where, String $value):bool
	{
		$this->build_model();
		return DATABASE->update($where,$value);
	}
	
}
?>