<?php

namespace ExpertCoder\SymapiSecurityBundle\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use ExpertCoder\SymapiSecurityBundle\Model\SymapiUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use ExpertCoder\SymapiSecurityBundle\Exception\UserNotActiveException;
use Doctrine\ORM\EntityRepository;

class UserManager
{
	private $jwtEncoder;
	private $userPasswordEncoder;

	public function __construct(JWTEncoderInterface $jwtEncoder, UserPasswordEncoderInterface $userPasswordEncoder)
	{
		$this->jwtEncoder = $jwtEncoder;
		$this->userPasswordEncoder = $userPasswordEncoder;
	}

	public function getAuthToken(SymapiUserInterface $user)
	{
		$token = $this->jwtEncoder->encode(['id' => $user->getId() ]);

		return $token;
	}

	public function getUserByCredentials(EntityRepository $repo, $email, $password)
	{
		//TODO - This bundle is still slightly coupled with the main application bundle here, because it assumes $repo->findOneByEmail() is going to be available
		$user = $repo->findOneByEmail($email);

		if (!$user) {
			throw new UsernameNotFoundException();
		}

		$isValid = $this->userPasswordEncoder->isPasswordValid($user, $password);

		if (!$isValid) {
			throw new BadCredentialsException();
		}

		if ($user->getStatus() != 'active') {
			throw new UserNotActiveException('Cannot log in, this user account is not set to active');
		}

		return $user;
	}

	public function generateEncodedUserPassword(SymapiUserInterface $user, $plainPassword)
	{
		return $this->userPasswordEncoder->encodePassword($user, $plainPassword);

	}

	public function generateResetToken()
	{
		//string length should be 13 + 40 ??
		return uniqid().bin2hex(random_bytes(20));
	}
}