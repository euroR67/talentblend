<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Langue;
use App\Entity\Metier;
use App\Entity\Niveau;
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
                'label' => false,
                'mapped' => false, 
                'attr' => [
                    'style' => 'display: none;', // Hide the field initially
                ],
            ])
            ->add('cv', FileType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('nom', TextType::class, [
                'label' => false,
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
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
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'anneeExperience',
                'multiple' => false,
                'expanded' => false,
                'label' => false,
            ])
            ->add('langues', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'langage',
                'multiple' => true,
                'expanded' => false,
                'label' => false,
            ])
            ->add('villes', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'multiple' => false,
                'expanded' => false,
                'label' => false,
            ])
            ->add('active', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => false,
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
