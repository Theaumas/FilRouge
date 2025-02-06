<?php

namespace App\Controller\Admin;

use App\Entity\Addresse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AddresseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Addresse::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW);
        
        // return $actions
        //     ->
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            TextField::new('adresse'),
            TextField::new('titre'),
            TextField::new('Ville'),
            TextField::new('CodePostal'),
            TextField::new('Pays'),
            AssociationField::new('user'),
        ];
    }
    
}
