<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class                    => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class                     => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class         => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class             => ['dev' => true, 'test' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class                            => ['dev' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class                              => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class                      => ['all' => true],
    Nelmio\CorsBundle\NelmioCorsBundle::class                                => ['all' => true],
    ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class                      => ['all' => true],
    EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle::class                   => ['all' => true],
    Knp\DoctrineBehaviors\DoctrineBehaviorsBundle::class                     => ['all' => true],
    Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class                => ['dev' => true, 'test' => true],
    Gesdinet\JWTRefreshTokenBundle\GesdinetJWTRefreshTokenBundle::class      => ['all' => true],
];
