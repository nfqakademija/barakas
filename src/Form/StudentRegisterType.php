<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class StudentRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('owner', TextType::class, [
                'label' => 'Vartotojo vardas',
                'attr' => [
                    'readonly' => true
                ],
                'data' => $options['owner']
            ])
            ->add('email', EmailType::class, [
                'label' => 'El. pašto adresas',
                'attr' => [
                    'readonly' => true
                ],
                'data' => $options['email']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Slaptažodis',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6]),
                ],
                'attr' => [
                    'placeholder' => 'Įrašykite savo slaptažodį'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'owner' => null,
            'email' => null
        ]);
    }
}
