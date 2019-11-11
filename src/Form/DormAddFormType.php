<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DormAddFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dname', TextType::class, [
                'label' => 'Bendrabučio pavadinimas',
                'attr' => [
                    'placeholder' => 'II Bendrabutis'
                ]
            ])
            ->add('daddr', TextType::class, [
                'label' => 'Bendrabučio adresas',
                'attr' => [
                    'placeholder' => 'P. Višinskio 15a'
                ]
            ])
        ;
    }
}
