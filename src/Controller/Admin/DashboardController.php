<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\RefreshToken;
use App\Entity\Transaction;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureMenuItems(): iterable
    {
        // Section pour gérer tous les objets liés à l'utilisateur
        yield MenuItem::section('Users');

        // CRUD User
        yield MenuItem::subMenu('Users', 'fas fa-user')->setSubItems([
            MenuItem::linkToCrud('Create User', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show Users', 'fas fa-eye', User::class),
        ]);

        // CRUD Account
        yield MenuItem::subMenu('Accounts', 'fas fa-address-book')->setSubItems([
            MenuItem::linkToCrud('Create Account', 'fas fa-plus', Account::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show Accounts', 'fas fa-eye', Account::class),
        ]);

        // CRUD Token
        yield MenuItem::subMenu('Tokens', 'fas fa-key')->setSubItems([
            MenuItem::linkToCrud('Show Tokens', 'fas fa-eye', RefreshToken::class),
        ]);

        // Section pour gérer tous les objets liés à la gestion des investissements
        yield MenuItem::section('Invests');

		// CRUS Holdings
	    yield MenuItem::subMenu('Holdings', 'fas fa-hand-holding-dollar')->setSubItems([
		    MenuItem::linkToCrud('Create Holding', 'fas fa-plus', Holding::class)->setAction(Crud::PAGE_NEW),
		    MenuItem::linkToCrud('Show Holdings', 'fas fa-eye', Holding::class),
	    ]);

        // CRUD Currencies
        yield MenuItem::subMenu('Currencies', 'fas fa-coins')->setSubItems([
            MenuItem::linkToCrud('Create Currency', 'fas fa-plus', Currency::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show Currencies', 'fas fa-eye', Currency::class),
        ]);

        // CRUD Transactions
        yield MenuItem::subMenu('Transactions', 'fas fa-money-bill-trend-up')->setSubItems([
            MenuItem::linkToCrud('Create Transaction', 'fas fa-plus', Transaction::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show Transactions', 'fas fa-eye', Transaction::class),
        ]);

        // CRUD Exchange
        yield MenuItem::subMenu('Exchanges', 'fas fa-arrow-right-arrow-left')->setSubItems([
            MenuItem::linkToCrud('Create Exchange', 'fas fa-plus', Exchange::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show Exchanges', 'fas fa-eye', Exchange::class),
        ]);
    }
}
