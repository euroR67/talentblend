<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Model\SearchData;
use App\Entity\TypeEmploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchEmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('poste', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher emplois',
                    'class' => 'form-control-search',
                ],
                'label' => false,
            ])
            ->add('ville', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir un lieu',
                    'class' => 'form-control-search',
                ],
                'label' => false,
            ])
            ->add('typeEmplois', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Temp plein' => 'Temp plein',
                    'Temp partiel' => 'Temp partiel',
                    'Télétravail' => 'Télétravail',
                    'Hybride' => 'Hybride',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('contrats', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Intérim' => 'Intérim',
                    'Alternance' => 'Alternance',
                    'Stage' => 'Stage',
                    'Freelance' => 'Freelance',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'csrf_protection' => false,
        ]);
    }
}
