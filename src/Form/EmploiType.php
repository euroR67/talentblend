<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Emploi;
use App\Entity\Niveau;
use App\Entity\Contrat;
use App\Entity\Categorie;
use App\Entity\Entreprise;
use App\Entity\TypeEmploi;
use EmptyToNullTransformer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EmploiType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('poste', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('disponibilite', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('salaire', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('salaireMinimum', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('showBy', ChoiceType::class, [
                'choices' => [
                    'Echelle' => 'echelle',
                    'Montant de départ' => 'montantMinimum',
                    'Montant maximale' => 'montantMaximum',
                    'Négociable' => 'negociable',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('taux', ChoiceType::class, [
                'choices' => [
                    'Par heure' => 'horaire',
                    'Par jour' => 'journalier',
                    'Par semaine' => 'hebdomadaire',
                    'Par mois' => 'mensuel',
                    'Par année' => 'annuel',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('contrats', EntityType::class, [
                'class' => Contrat::class,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('entreprise', EntityType::class, [
                'class' => Entreprise::class,
            ])
            ->add('types', EntityType::class, [
                'class' => TypeEmploi::class,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
        // On récupère l'utilisateur, on sanitize pour s'assurer qu'un utilisateur existe
        $user = $this->security->getUser();
        if(!$user) {
            throw new \LogicException(
                'Le form EmploiType ne peu être utilisé sans utilisateur authentifié !'
            );
        }
        // Permet l'affichage des entreprises que représente un recruteur
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user): void {
            $form = $event->getForm();
        
            $form->add('entreprise', EntityType::class, [
                'class' => Entreprise::class,
                'multiple' => false,
                'expanded' => false,
                // On récupère les entreprises que représente l'utilisateur
                'choices' => $user->getEntrepriseRepresenter()->filter(function ($representant) {
                    $entreprise = $representant->getEntreprise();
                    
                    // On renvoie les entreprises qui sont validées et dont la représentation est validé
                    return $representant->isStatus() && $entreprise->isIsVerified();
                })->map(function ($representant) {
                    return $representant->getEntreprise();
                })->toArray(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emploi::class,
        ]);
    }
}
