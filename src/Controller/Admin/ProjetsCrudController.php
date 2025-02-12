<?php

namespace App\Controller\Admin;

use App\Entity\Projets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class ProjetsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Projets::class;
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
            TextField::new('Nom'),
            TextEditorField::new('Description'),
            DateField::new('DateLimite'),
            TextField::new('Membres'),
            DateField::new('DateCreation'),
        ];
    }
    
}
