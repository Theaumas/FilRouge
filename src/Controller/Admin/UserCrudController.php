<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }
    
    
    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            TextField::new('matricule'),
            TextField::new('email'),
            TextField::new('password'),
            TextField::new('Prenom'),
            TextField::new('Nom'),
            DateField::new('DateDeNaissance'),
            TextField::new('Tel'),
            TextField::new('service'),
            ChoiceField::new('roles')
            ->setLabel('Permissions')
            ->setHelp('Choix des rôles des membres')
            ->setChoices([
            'ROLE_ADMIN' => 'ROLE_ADMIN',
            'ROLE_CHEF' => 'ROLE_CHEF',
            'ROLE_USER' => 'ROLE_USER',
        ])->allowMultipleChoices(),
        ];
    }
    
}
