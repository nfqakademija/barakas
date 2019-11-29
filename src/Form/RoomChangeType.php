<?php

namespace App\Form;

use App\Entity\RoomChange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_room_nr', TextType::class, [
                'label' => 'Naujo kambario numeris',
                'attr' => [
                    'placeholder' => 'pvz. 504B'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Keitimo priežastis',
                'attr' => [
                    'placeholder' => 'pvz. Persikrausčiau į kitą kambarį'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoomChange::class,
        ]);
    }
}
