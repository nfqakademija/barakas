<?php

namespace App\Form;

use App\Entity\Organisation;
use App\Entity\Traits\AcademiesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
         /*   ->add('academyTitle', ChoiceType::class, [
                'label' => 'Mokymo įstaigos pavadinimas',
                'choices' => [
                    'Universitetai' => Academies::getUniversities(),
                    'Kolegijos' => Academies::getColleges()
                ]
            ])*/
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
