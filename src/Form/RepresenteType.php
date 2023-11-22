<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Represente;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RepresenteType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $representation = $options['data'];

        $isEditMode = $representation && $representation->getId();

        $builder
            ->add('entreprise', EntityType::class, [
                'class' => Entreprise::class,
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => function (EntityRepository $er) use ($user, $isEditMode, $representation) {
                    $qb = $er->createQueryBuilder('e')
                        ->leftJoin('e.representants', 'r')
                        ->where('e.isVerified = :isVerified')
                        ->setParameter('isVerified', true);

                    if ($isEditMode) {
                        $qb->andWhere('e = :currentEntreprise OR (r.userEntreprise != :userEntreprise OR r.userEntreprise IS NULL)')
                            ->setParameter('currentEntreprise', $representation->getEntreprise());
                    } else {
                        $qb->andWhere('r.userEntreprise != :userEntreprise OR r.userEntreprise IS NULL');
                    }

                    return $qb->setParameter('userEntreprise', $user);
                },
                'disabled' => $isEditMode && $representation->isStatus() == 0,
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
        // Si status de Represente est égal a 0, on disable le champ Entreprise
        // Car il s'agit donc d'une demande de réaxamination de représentation
        // Et donc l'utilisateur n'est pas censé pouvoir changer l'entreprise a représenter

        // Dans le champ Entreprise, afficher uniquement les entreprise dont le status de la méthode isIsVerified
        // Est égal a TRUE ou 1 , et aussi , ne pas afficher les entreprise que l'utilisateur en session
        // Représente déjà.
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Represente::class,
        ]);
    }
}
