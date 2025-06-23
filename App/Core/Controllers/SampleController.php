<?php
namespace App\Core\Controllers;

use App\Core\Classes\Controller;
use App\Core\Models\SampleModel;
require_once __DIR__ . '/../Classes/Controller.php';
require_once __DIR__ . '/../Models/SampleModel.php';


class SampleController extends Controller
{
	function __construct(protected String $model_name = "sample")
	{
		$sampleModel = new SampleModel();
		$this->set_model($sampleModel);
	}
	public function getAll()
	{
		$data = [
			"Sample1"=>"sup",
			"Sample2"=>"samples",
			"Sample3"=>"are",
			"Sample4"=>"working"
		];
		echo json_encode($data);
		return json_encode($data);
	}

	public function findId($id)
	{
		echo json_encode([$id]);
		return json_encode([$id]);
	}
}
