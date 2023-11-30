<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchCandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('metiers', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher candidats',
                ],
            ])
            ->add('villes', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir un lieu',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
