<?php
namespace App\Core\Controllers;

use App\Core\Classes\Controller;
use App\Core\Models\SampleModel;
require_once __DIR__ . '/../Classes/Controller.php';
require_once __DIR__ . '/../Models/SampleModel.php';


class SampleController extends Controller
{
	function __construct()
	{
		$sampleModel = new SampleModel();
		$this->setModel($sampleModel);
	}
	public function getAll() : string
	{
		$data = $this->model->select(["*"]);
		$response = json_encode($data);
		echo $response;
		return $response;
	}

	public function findId(string $id) : string
	{
		$data = $this->model->findId(["*"],$id);
		$response = json_encode($data);
		echo $response;
		return $response;
	}
	public function delete(string $id)
	{
		return $this->model->deleteId($id);
	}
}
