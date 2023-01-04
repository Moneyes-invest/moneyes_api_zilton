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
final class Version20230104195104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE holding_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE holding (id INT NOT NULL, id_user_id UUID NOT NULL, id_currency_id UUID NOT NULL, id_exchange_id UUID NOT NULL, quantity NUMERIC(10, 2) NOT NULL, average_purchase_price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BBFD81679F37AE5 ON holding (id_user_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD81620364C81 ON holding (id_currency_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD81670BD1C57 ON holding (id_exchange_id)');
        $this->addSql('COMMENT ON COLUMN holding.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.id_currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.id_exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81679F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81620364C81 FOREIGN KEY (id_currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81670BD1C57 FOREIGN KEY (id_exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE holding_id_seq CASCADE');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81679F37AE5');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81620364C81');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81670BD1C57');
        $this->addSql('DROP TABLE holding');
    }
}
