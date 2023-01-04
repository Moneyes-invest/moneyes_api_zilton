<?php

namespace App\Controller\Admin;

use App\Entity\Holding;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class HoldingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Holding::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
	        AssociationField::new('idUser'),
	        AssociationField::new('idCurrency'),
	        AssociationField::new('idExchange'),
	        NumberField::new('quantity'),
	        MoneyField::new('averagePurchasePrice')->setCurrency('EUR'),
        ];
    }
}
