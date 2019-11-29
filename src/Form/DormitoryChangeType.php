<?php

namespace App\Form;

use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DormitoryChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dormitory', EntityType::class, [
                'label' => 'Pasirinkite adresą',
                'class' => Dormitory::class,
                'choice_label' => 'address',
                'choice_value' => 'id',
                'choices' => [
                    'Gatvė' => $options['dorms']
                ]
            ])
            ->add('room_nr', TextType::class, [
                'label' => 'Kambario numeris',
                'attr' => [
                    'placeholder' => '505A'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Keitimo priežastis',
                'attr' => [
                    'placeholder' => 'Nurodykite priežastį dėl kurios keičiate bendrabutį'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DormitoryChange::class,
            'dorms' => null,
            'academy' => null
        ]);
    }
}
