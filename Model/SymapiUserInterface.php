<?php

namespace ExpertCoder\SymapiSecurityBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface SymapiUserInterface extends UserInterface
{
	public function getId();

	public function getStatus();

	public function getPlainPassword();

	public function setEncodedPassword();


}