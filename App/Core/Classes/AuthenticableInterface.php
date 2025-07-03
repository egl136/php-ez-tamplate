<?php
namespace App\Core\Classes\Auth;

interface AuthenticableInterface
{
	public function getAuthIdentifierName(): string;
	public function getAuthIdentifier();
	public function getAuthPassword(): string;
	public function getRememberToken(): ?string;
	public function setRememberToken(string $value): void;
	public function getRememberTokenName(): string;
}