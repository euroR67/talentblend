<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Langue;
use App\Entity\Metier;
use App\Entity\Niveau;
use App\Entity\Contrat;
use App\Entity\TypeEmploi;
use App\Form\FormationType;
use App\Form\ExperienceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Le format de l\'photo n\'est pas valide !',
                    ])
                ],
            ])
            ->add('deletePhoto', CheckboxType::class, [
                'required' => false,
                'label' => 'Supprimer la photo',
                'mapped' => false, 
                'attr' => [
                    'style' => 'display: none;', // Hide the field initially
                ],
            ])
            ->add('cv', FileType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Le format du CV n\'est pas valide !',
                    ])
                ],
            ])
            ->add('deleteCV', CheckboxType::class, [
                'required' => false,
                'label' => 'Supprimer le CV',
                'mapped' => false, 
                'attr' => [
                    'style' => 'display: none;', // Hide the field initially
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'disabled' => true,
            ])
            ->add('metiers', EntityType::class, [
                'class' => Metier::class,
                'choice_label' => 'nomMetier',
                'multiple' => false,
                'expanded' => false,
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez votre métier',
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'anneeExperience',
                'multiple' => false,
                'expanded' => false,
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez votre niveau',
            ])
            ->add('contrats', EntityType::class, [
                'class' => Contrat::class,
                'choice_label' => 'type',
                'multiple' => true,
                'expanded' => false,
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez contrat(s)',
            ])
            ->add('typesEmploi', EntityType::class, [
                'class' => TypeEmploi::class,
                'choice_label' => 'type',
                'multiple' => true,
                'expanded' => false,
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez type(s) emploi',
            ])
            ->add('langues', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'langage',
                'multiple' => true,
                'expanded' => false,
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez langue(s)',
            ])
            ->add('villes', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'multiple' => false,
                'expanded' => false,
                'label' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez une ville',
            ])
            ->add('active', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'attr' => ['class' => 'custom-choice'],
                'expanded' => true,
                'multiple' => false,
                'label' => false,
            ])
            ->add('formations', CollectionType::class, [
                'entry_type' => FormationType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'by_reference' => false,
                'error_bubbling' => false,
                'prototype' => true,
            ])
            ->add('experiences', CollectionType::class, [
                'entry_type' => ExperienceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'by_reference' => false,
                'error_bubbling' => false,
                'prototype' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
            
                // Check if the user has a photo
                $hasPhoto = $user && $user->getPhoto();
            
                // Check if the user has a CV
                $hasCV = $user && $user->getCv();
            
                // Add or modify the deletePhoto field
                $form->add('deletePhoto', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                    'mapped' => false,
                    'attr' => [
                        'style' => $hasPhoto ? 'display: block;' : 'display: none;',
                    ],
                ]);
            
                // Add or modify the deleteCV field
                $form->add('deleteCV', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                    'mapped' => false,
                    'attr' => [
                        'style' => $hasCV ? 'display: block;' : 'display: none;',
                    ],
                ]);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
