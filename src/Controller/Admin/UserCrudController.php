<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Formation;
use App\Entity\Experience;
use App\Form\FormationType;
use App\Form\ExperienceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Utilisateur')
                ->setIcon('fas fa-user-tie')->addCssClass('text-success'),
            FormField::addColumn(6),
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            ArrayField::new('roles'),
            TextField::new('password')->hideOnIndex(),
            TextField::new('nom'),
            TextField::new('prenom'),
            AssociationField::new('ville')->onlyOnForms(),
            ImageField::new('photo')
                ->setBasePath('pdp/')
                ->setUploadDir('public/pdp')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->onlyOnForms(),
            TextareaField::new('description')->onlyOnForms(),
            TextField::new('cv')->onlyOnForms(),
            BooleanField::new('active')->setLabel('Profil en ligne'),
            FormField::addColumn(4),
            CollectionField::new('experiences')->onlyOnForms()
                ->setEntryType(ExperienceType::class)
                ->setFormTypeOption('by_reference', false),
            CollectionField::new('formations')->onlyOnForms()
                ->setEntryType(FormationType::class)
                ->setFormTypeOption('by_reference', false),
            AssociationField::new('metier')->onlyOnForms(),
            AssociationField::new('langues')
                ->onlyOnForms()
                ->setFormTypeOptions(['multiple' => true]),
            AssociationField::new('typesEmploi')
                ->onlyOnForms()
                ->setFormTypeOptions(['multiple' => true]),
            AssociationField::new('contrats')
                ->onlyOnForms()
                ->setFormTypeOptions(['multiple' => true]),
            AssociationField::new('niveau')->onlyOnForms(),

            FormField::addTab('Recruteur')
                ->setIcon('fas fa-user-tie')->addCssClass('text-success'),
            AssociationField::new('entrepriseRepresenter')
                ->onlyOnForms()
                ->setFormTypeOptions(['by_reference' => false]),
            AssociationField::new('emplois')
                ->onlyOnForms()
                ->setFormTypeOptions(['by_reference' => false]),
            
            AssociationField::new('entrepriseCreator')
                ->onlyOnForms()
                ->setFormTypeOptions(['by_reference' => false]),
        ];
    }
}
