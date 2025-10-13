<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Enter New Password',
                    'attr' => [
                        'class' => 'form-control mb-3',
                        'placeholder' => 'New Password',
                    ],
                ],
                'second_options' => [
                    'label' => 'Re-enter Password',
                    'attr' => [
                        'class' => 'form-control mb-3',
                        'placeholder' => 'Confirm Password',
                    ],
                ],
                'invalid_message' => 'Passwords must match.',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}


