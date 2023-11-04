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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'required' => false,
            ])
            ->add('cv', FileType::class, [
                'required' => false,
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('metiers', EntityType::class, [
                'class' => Metier::class,
                'choice_label' => 'nomMetier',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('description', TextareaType::class)
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'anneeExperience',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('langues', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'langage',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('villes', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nomVille',
                'multiple' => true,
                'expanded' => true,
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
