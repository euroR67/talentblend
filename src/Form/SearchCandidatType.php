<?php

namespace App\Form;

use App\Entity\Niveau;
use App\Entity\Contrat;
use App\Model\SearchData;
use App\Entity\TypeEmploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchCandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Form with metier and ville fields
        $builder
            ->add('metier', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher candidats',
                ],
            ])
            ->add('ville', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir un lieu',
                ],
            ])
            ->add('typesEmploi', EntityType::class, [
                'class' => TypeEmploi::class,
                'required' => false,
                'label' => false,
                'choice_label' => 'type',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('contrats', EntityType::class, [
                'class' => Contrat::class,
                'required' => false,
                'label' => false,
                'choice_label' => 'type',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'required' => false,
                'label' => false,
                'choice_label' => 'anneeExperience',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
