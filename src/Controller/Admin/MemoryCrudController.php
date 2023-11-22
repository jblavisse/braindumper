<?php

namespace App\Controller\Admin;

use App\Entity\Memory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MemoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Memory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Memory')
            ->setEntityLabelInPlural('Memories');
        
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'Id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description'),
            AssociationField::new('user',"Email de l'utilisateur")
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('choice_label', 'email'),
            AssociationField::new('category', 'Nom de la catégorie')
                ->setCrudController(CategoryCrudController::class)
                ->setFormTypeOption('choice_label', 'name'), 
            AssociationField::new('type', 'Nom du type')
                ->setCrudController(TypeCrudController::class) 
                ->setFormTypeOption('choice_label', 'name'),
        ];
    }
    
}