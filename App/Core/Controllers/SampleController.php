<?php
namespace App\Core\Controllers;

use App\Core\Classes\Request;
use App\Core\Classes\Controller;
use App\Core\Models\SampleModel;

require_once __DIR__ . '/../Classes/Request.php';
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

	public function store(Request $request)
	{
		$expected = $request->validate([
			'name' => ['required','string','min:2', '10']
		]);
		$name = $request->input('name');
		$id = $request->input('id');
		$stored = $this->model->register(['id' => $id, 'name' => $name]);
		$response = $stored ? ['message' => "Item $id created."] : ['message' => "Failed to create."];
	}
	public function delete(string $id)
	{
		return $this->model->deleteId($id);
	}
	public function testCookies()
	{
		require_once __DIR__.'/../../../test/28JUN2025/coockies.php';
	}
	public function testCookiesReg($cookie)
	{
		require_once __DIR__.'/../../../test/28JUN2025/coockies.php';
	}
}
