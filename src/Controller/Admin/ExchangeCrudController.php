<?php

namespace App\Controller\Admin;

use App\Entity\Exchange;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ExchangeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exchange::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
