<?php

namespace App\Form;

use App\Entity\Ville;
use App\Entity\Taille;
use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logo', FileType::class, [
                'required' => false,
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
            ->add('banniere', FileType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
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
            ->add('raisonSocial', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('secteur', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('website', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'attr' => [
                    'class' => 'form-control'
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('tailles', EntityType::class, [
                'class' => Taille::class,
                'attr' => [
                    'class' => 'form-control'
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('kbis', FileType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
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
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
