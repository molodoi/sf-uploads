<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastname', TextType::class, [
            'label' => 'Lastname',
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 2,
                ]),
            ],
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Firstname',
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 2,
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ])
        ->add('password', PasswordType::class, [
            'mapped' => false,
            'required' => false,
            'label' => 'Password',
            'attr' => [
                'type' => 'password',
            ],
            'constraints' => [
                new Length([
                    'min' => 6,
                ]),
            ],
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'USER' => 'ROLE_USER',
                'ADMIN' => 'ROLE_ADMIN',
            ],
            'multiple' => true,
        ])
        ->add('is_verified', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false,
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
