<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert; 

class ForgotPasswordRequestType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
            'label' => 'Email address',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please enter your email address.',
                ]),
                new Assert\Email([
                    'message' => 'Please enter a valid email address.',
                ]),
            ],
            'attr' => [
                'autocomplete' => 'email',
                'placeholder' => 'yourusername@afrihost.com',
                'class' => 'form-control',
            ],
        ]);
    }
}