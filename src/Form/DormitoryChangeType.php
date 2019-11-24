<?php

namespace App\Form;

use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DormitoryChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('academy', EntityType::class, [
                'label' => 'Adresas',
                'class' => Dormitory::class,
                'choice_label' => 'address',
                'choice_value' => 'address',
                'choices' => [
                    'Gatvė' => $options['dorms']
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DormitoryChange::class,
            'dorms' => null
        ]);
    }
}
