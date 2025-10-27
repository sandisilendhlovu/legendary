<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // First Name
            ->add('firstName', TextType::class, [
            'label' => 'Name',
            'attr' => ['autocomplete' => 'given-name'],
            'constraints' => [
              new NotBlank ([
                'message' => 'Please enter your first name.',
              ]),
            ],
        ])
          
           // Last Name  
            ->add('lastName', TextType::class, [
              'label' => 'Surname',
              'attr' => ['autocomplete' => 'family-name'],
              'constraints' => [
                new NotBlank([
                    'message' => 'Please enter your surname.',
                ]), 
            ],
          
        ])
             // Email
            ->add('email', EmailType::class, [
                'label' => 'Afrihost Email Address',
                'attr' => ['autocomplete' => 'username'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Afrihost email address.'
                        
                    ]),
                ],
            ])
                 // Password
               ->add('plainPassword', PasswordType::class, [
                'label' => 'Password',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' =>'Your password must be at least {{ limit }} characters long.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

              // Terms Checkbox
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to the Terms and Conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must agree to the terms and conditions to register.',
                    ]),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
