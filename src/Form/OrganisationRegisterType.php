<?php

namespace App\Form;

use App\Entity\Organisation;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('owner', TextType::class, [
                'label' => 'Vadovo vardas ir pavardė',
                'attr' => [
                    'placeholder' => 'pvz. Jonas Jonaitis'
                ]
            ])
            ->add('academyTitle', TextType::class, [
                'label' => 'Mokymo įstaigos pavadinimas',
                'attr' => [
                    'placeholder' => 'pvz. Vilniaus Universitetas'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresas',
                'attr' => [
                    'placeholder' => 'pvz. Vilnius, Universisteto g. 3'
                ]
            ])
            ->add('dormitoryCount')
            ->add('Registruoti organizaciją', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organisation::class,
        ]);
    }
}
