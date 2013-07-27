<?php
// src/Tk/UserBundle/Form/Type/RegistrationFormType.php

namespace Tk\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'form.username', 
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    'placeholder' => 'Username',
                ),
            ))
            ->add('email', 'repeated', array(
                'type' => 'email',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => ' ', 'attr' => array(
                    'placeholder' => 'Email',
                )),
                'second_options' => array('label' => ' ', 'attr' => array(
                    'placeholder' => 'Confirm email',
                )),
                'invalid_message' => 'Emails do not match',
            ))
            ->add('plainPassword', 'password', array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'form.password',
                'attr' => array(
                    'placeholder' => 'Password',
                ),
            ))
        ;
    }

    public function getName()
    {
        return 'tk_user_registration';
    }
}