<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                 'invalid_message' => 'Passwords must match.',
            'mapped' => false,
            'constraints' => [  
                new Assert\NotBlank(['message' => 'Please enter a password']),
                new Assert\Length([
                    'min' => 8,
                    'minMessage' => 'Your password must be at least {{ limit }} characters long',
                    'max' => 4096,
                ]),
            ],
            'first_options' => [
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
       $resolver->setDefaults([
            'validation_groups' => ['Default'],
            'constraints' => [new Assert\Valid()],
        ]);
    }
}
