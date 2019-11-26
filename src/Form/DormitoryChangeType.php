<?php

namespace App\Form;

use App\Entity\Academy;
use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DormitoryChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
/*            ->add('academy', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => 'Akademija',
                'attr' => [
                    'readonly' => true
                ],
                'data' => 'academy'
            ])*/
            ->add('dormitory', EntityType::class, [
                'label' => 'Adresas',
                'class' => Dormitory::class,
                'choice_label' => 'organisation_id',
                'choice_value' => 'organisation_id',
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
            'dorms' => null,
            'academy' => null
        ]);
    }
}
