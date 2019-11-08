<?php

namespace App\Form;

use App\Entity\Academy;
use App\Entity\Organisation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ->add('email', EmailType::class, [
                'label' => 'El. pašto adresas',
                'attr' => [
                    'placeholder' => 'pvz. jonas@example.com'
                ]
            ])
            ->add('academy', EntityType::class, [
                'label' => 'Mokymo įstaiga',
                'class' => Academy::class,
                'choice_label' => 'title',
                'choice_value' => 'title'

            ])
            ->add('save', SubmitType::class, [
                'label' => 'Registruoti organizaciją',
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
