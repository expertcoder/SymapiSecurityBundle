<?php

namespace ExpertCoder\SymapiSecurityBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class LoginModel
{
    /**
     * @Assert\Email()
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


}