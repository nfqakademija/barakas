<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class SendInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'style' => 'width:40%;',
                    'placeholder' => 'Vardas ir Pavardė',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('mail', EmailType::class, [
                'label' => false,
                'attr' => [
                    'style' => 'width:40%;',
                    'placeholder' => 'El pašto adresas',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('room', TextType::class, [
                'label' => false,
                'attr' => [
                    'style' => 'width:25%;',
                    'placeholder' => 'Paskirtas kambarys',
                    'autocomplete' => 'off'
                ]
            ]);
    }
}
