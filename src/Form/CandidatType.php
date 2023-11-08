<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Langue;
use App\Entity\Metier;
use App\Entity\Niveau;
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
                'label' => 'Votre photo',
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
                'label' => 'Votre CV',
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
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'disabled' => true,
            ])
            ->add('metiers', EntityType::class, [
                'class' => Metier::class,
                'choice_label' => 'nomMetier',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Métier',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'A propos de vous',
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'anneeExperience',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Années d\'expérience',
            ])
            ->add('langues', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'langage',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Langues',
            ])
            ->add('villes', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Ville',
            ])
            ->add('active', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Activer mon profil',
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
            // Add an event listener to conditionally display deletePhoto
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
    
                // Check if the user has a photo
                $hasPhoto = $user && $user->getPhoto();
    
                // if hasPhoto is true , set deletePhoto attr style to display: block
                // else set it to display: none
                $form->add('deletePhoto', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                    'mapped' => false, 
                    'attr' => [
                        'style' => $hasPhoto ? 'display: block;' : 'display: none;',
                    ],
                ]);
            })
            // Add an event listener to conditionally display deleteCV
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
    
                // Check if the user has a cv
                $hasCV = $user && $user->getCv();
    
                // if hasCV is true , set deleteCV attr style to display: block
                // else set it to display: none
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
