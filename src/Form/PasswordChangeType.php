<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Senas slaptažodis',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'placeholder' => 'Senas slaptažodis'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Naujas slaptažodis',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6]),
                ],
                'attr' => [
                    'placeholder' => 'Naujas slaptažodis'
                ]
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Patvirtinti slaptažodį',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6]),
                ],
                'attr' => [
                    'placeholder' => 'Patvirtinti slaptažodį'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Keisti',
            ]);
    }

}
