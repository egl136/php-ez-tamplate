<?php
namespace App\Core\Classes\Auth;
trait Authenticable
{
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return $this->{$this->getRememberTokenName()};
    }

    public function setRememberToken(string $value): void
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}