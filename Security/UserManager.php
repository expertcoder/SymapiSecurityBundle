<?php

namespace ExpertCoder\SymapiSecurityBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
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

class UserManager
{
	private $jwtEncoder;
	private $userPasswordEncoder;

	public function __construct(JWTEncoderInterface $jwtEncoder, UserPasswordEncoderInterface $userPasswordEncoder)
	{
		$this->jwtEncoder = $jwtEncoder;
		$this->userPasswordEncoder = $userPasswordEncoder;
	}

	public function getAuthToken(UserInterface $user)
	{
		$token = $this->jwtEncoder->encode(['id' => $user->getId() ]);

		return $token;
	}

	public function getUserByCredentials(EntityManager $em, $email, $password)
	{
		$user = $em->getRepository('AppBundle:User')->findOneByEmail($email); //NOTE: if you wanted to make this bundle stand alone will need to remove dependency on the AppBundle

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

	public function generateEncodedUserPassword(User $user, $plainPassword)
	{
		return $this->userPasswordEncoder->encodePassword($user, $plainPassword);

	}

	public function generateResetToken()
	{
		//string length should be 13 + 40 ??
		return uniqid().bin2hex(random_bytes(20));
	}
}