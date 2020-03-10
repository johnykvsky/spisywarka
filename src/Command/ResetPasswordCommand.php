<?php

namespace App\Command;

class ResetPasswordCommand implements CommandInterface
{
    /**
     * @var string
     */
    private $email;
    
    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string 
    {
        return $this->email;
    }
}
