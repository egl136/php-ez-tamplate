<?php
namespace App\Core\Models;

use App\Core\Classes\Model;
use App\Core\Classes\Auth\AuthenticableInterface;
use App\Core\Classes\Auth\Authenticable;

require_once __DIR__ . '/../Classes/Model.php';
require_once __DIR__ . '/../Classes/AuthenticableInterface.php';
require_once __DIR__ . '/../Classes/Authenticable.php';

class UserModel extends Model implements AuthenticableInterface
{
	public ?String $userId;
	public ?String $userName;
	public ?String $userMail;
	public ?String $userNick;
	public ?String $userPassowrd;
	public ?String $userAgent;
	
	use Authenticable;

	function __construct(protected String $model_name = "users", bool $generateId = false, int $idLen = 5, string $idType = "alphanumerical")
	{
		$this->idColumn = "user_id";
		$this->model_name = $model_name;
		parent::__construct($model_name);
	}
	public function signUp() : bool
	{
		$this->userId = $this->generateId(6);
		$userStored = $this->saveUser([
			'user_id' => $this->userId,
			'user_name' => $this->userName, 
			'user_mail' => $this->userMail,
			'user_nick' => $this->userNick,
		]);
		if($userStored){
			return $this->signCredentials($this->userId,$this->userPassword,$this->userAgent);
		}
		return false;
	}
	private function signCredentials(string $userid, string $password, string $useragent) : bool
	{
		$hashed = password_hash($password, PASSWORD_BCRYPT);
		$auth = new Model("auth");
		$signed = $auth->register([
			'user_id'=>$userid,
			'client_agent'=>$useragent,
			'hashed_password'=>$hashed
		]);
		if($signed){
			return true;
		}
		$deleted = $this->signFailed($userid);
		//TODO
		return false;
	}
	private function signFailed(string $userid) : bool
	{
		return $this->deleteId($userid);
	}
	private function saveUser(array $data) : bool
	{
		return $this->register($data);
	}
	public function getUser()
	{
		return $this->findId(["*"],$this->ueserId);
	}
}
