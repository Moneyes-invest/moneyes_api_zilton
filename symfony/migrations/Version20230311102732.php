<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230311102732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ADD synchro JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE account ALTER exchange_id DROP NOT NULL');
        $this->addSql('ALTER TABLE binance_account ADD synchro JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE holding DROP average_purchase_price');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE binance_account DROP synchro');
        $this->addSql('ALTER TABLE account DROP synchro');
        $this->addSql('ALTER TABLE account ALTER exchange_id SET NOT NULL');
        $this->addSql('ALTER TABLE holding ADD average_purchase_price DOUBLE PRECISION NOT NULL');
    }
}
