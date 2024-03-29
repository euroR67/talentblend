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
use Symfony\Component\Validator\Constraints\NotBlank;
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
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une entreprise.',
                    ]),
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
                'required' => true,
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
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Represente::class,
        ]);
    }
}
