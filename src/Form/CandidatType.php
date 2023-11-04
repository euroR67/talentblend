<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Langue;
use App\Entity\Metier;
use App\Entity\Niveau;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ])
            ->add('cv', FileType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('metiers', EntityType::class, [
                'class' => Metier::class,
                'choice_label' => 'nomMetier',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('description', TextareaType::class)
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'anneeExperience',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('langues', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'langage',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('villes', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
