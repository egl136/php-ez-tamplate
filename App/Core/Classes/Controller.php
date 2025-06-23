<?php 
namespace App\Core\Classes;

class Controller
{
	protected Array $fields;
	protected Array $values;
	protected Model $model;
	public $message;
	function __construct()
	{
		
	}
	protected function set_model(Model $model):void
	{
		$this->model = $model;
	}
	public function select(String $where = "1=1", String $order_by = "", String $order_type = "ASC",  String $limit = "100"):Array
	{
		$this->model->set_fields($this->fields);
		return $this->model->retrieve_all($where,$order_by,$order_type,$limit);
	}
	public function select_row(String $where):Array
	{
		$this->model->set_fields($this->fields);
		$this->model->build_model();
		return $this->model->retrieve_one($where);
	}
	public function save():bool
	{
		$saved = false;

		$this->model->set_fields($this->fields);
		if($this->verify_posts()){
			
			$this->model->set_values($this->values);
			
		
			$saved = $this->model->register();
			
		}

		return $saved;
	}

	public function set_model_fields(Array $fields):void
	{
		$this->fields = $fields;
	}
	
	public function set_model_values(Array $values):void
	{
		$this->values = $values;
	}
	
	protected function is_post():bool
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	private function verify_posts():bool
	{
		$posts_exist = false;
		if($this->is_post()){
			$n = 0;
			foreach ($this->model->get_fields() as $field) {
				if(empty($_POST[$field]) && !isset($_POST[$field])){
					return false;
				}
				$this->values[$n] = "'{$_POST[$field]}'";
				$n++;
			}
			$posts_exist = true;
		}
		
		return $posts_exist;
	}
	
	protected function get_exists($variable):bool
	{
		return !empty($_GET[$variable]) && isset($_GET[$variable]);
	}
}
?>
