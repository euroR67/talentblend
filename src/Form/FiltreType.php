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

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            // Champ pour filtrer par type d'emploi
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
            // Champ pour filtrer par type de contrat
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
            // Champ pour filtrer par date de publication
            ->add('dateOffre', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Aujourd\'hui' => 'Aujourd\'hui',
                    '3 derniers jours' => '3 derniers jours',
                    'La semaine dernière' => 'La semaine dernière',
                ],
                'data' => 'Toutes dates',
                'multiple' => false,
                'expanded' => true,
                'placeholder' => 'Toutes dates', // Ajoutez cette ligne
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
