<?php
namespace App\Core\Controllers;

use App\Core\Classes\Request;
use App\Core\Classes\Controller;
use App\Core\Classes\View;
use App\Core\Models\UserModel;

require_once __DIR__ . '/../Classes/Request.php';
require_once __DIR__ . '/../Classes/Controller.php';
require_once __DIR__ . '/../Classes/View.php';
require_once __DIR__ . '/../Models/UserModel.php';


class UserController extends Controller
{
	function __construct()
	{
		$sampleModel = new UserModel();
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
		$this->model->userId = $id;
		$data = $this->model->findId(["*"],$id);
		$response = json_encode($data);
		echo $response;
		return $response;
	}

	//POST EXAMPLE
	public function store(Request $request) : string
	{
		//LIKE IN LARAVEL, INPUT VALIDATION
		$expected = $request->validate([
			'user_name' => ['required','string','min:3', 'max:30'],
			'user_mail' => ['required','email', 'max:90'],
			'user_nick' => ['required','string','min:3', 'max:12'],
			'user_password' => ['required','string','min:8', 'max:25']
		]);
		$this->model->userName = $request->input('user_name');
		$this->model->userMail = $request->input('user_mail');
		$this->model->userNick = $request->input('user_nick');
		$this->model->userPassword = $request->input('user_password');

		//FOR THIS EXAMPLE, USER AGENT IS SAVED FOR AUTH
		$this->model->userAgent = $request->userAgent();

		$stored = $this->model->signUp();
		$response = $stored ? json_encode(['message' => "Item  created."]) : json_encode(['message' => "Failed to create."]);
			
		echo $response;
		return $response;
	}
	public function delete(string $id)
	{
		return $this->model->deleteId($id);
	}
	public function signUpForm()
	{
		View::render("signUpForm.php");
	}
	
}
