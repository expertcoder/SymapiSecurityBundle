<?php

namespace ExpertCoder\SymapiSecurityBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ExpertCoder\SymapiSecurityBundle\Model\SymapiUserInterface;
use ExpertCoder\SymapiSecurityBundle\Security\UserManager;

class DoctrineUserSubscriber implements EventSubscriber
{
	private $userManager;

	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}

	public function getSubscribedEvents()
	{
		return ['preUpdate','prePersist'];
	}


	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof SymapiUserInterface) {
			$this->handleEvent($entity);
		}
	}

	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof SymapiUserInterface) {
			$this->handleEvent($entity);
		}
	}

	private function handleEvent(SymapiUserInterface $user)
	{
		//If the plainPassword attribute is set, then assume a new password is been set
		$plainPassword = $user->getPlainPassword();

		if ($plainPassword) {
			$encodedPassword = $this->userManager->generateEncodedUserPassword($user, $plainPassword);
			$user->setEncodedPassword($encodedPassword);
		}
	}
}