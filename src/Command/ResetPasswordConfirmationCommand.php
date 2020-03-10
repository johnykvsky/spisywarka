<?php

namespace App\Command;

class ResetPasswordConfirmationCommand implements CommandInterface
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $password;
    
    /**
     * @param string $token
     * @param string $password
     */
    public function __construct(string $token, string $password)
    {
        $this->token = $token;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string 
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getToken(): string 
    {
        return $this->token;
    }
}
