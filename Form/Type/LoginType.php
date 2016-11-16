<?php

namespace ExpertCoder\SymapiSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', TextType::class, ['required'=> true ] )
			->add('password', TextType::class, ['required'=> true ])
		;
	}

//	public function configureOptions(OptionsResolver $resolver)
//	{
//		parent::configureOptions($resolver);
//	}


}