<?php

namespace App\Controller\Admin;

use App\Entity\RefreshToken;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RefreshTokenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RefreshToken::class;
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
