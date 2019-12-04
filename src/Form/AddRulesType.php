<?php

namespace App\Form;

use App\Entity\Dormitory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddRulesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rules', TextareaType::class, [
                'label' => 'Bendrabučio taisyklės:',
                'attr' => [
                    'placeholder' => 'Taisyklės',
                    'rows' => 15
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dormitory::class,
        ]);
    }
}
