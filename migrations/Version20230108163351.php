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
final class Version20230108163351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE holding_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE account (id UUID NOT NULL, id_exchange_id UUID NOT NULL, id_user_id UUID NOT NULL, private_key VARCHAR(255) DEFAULT NULL, public_key VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D3656A470BD1C57 ON account (id_exchange_id)');
        $this->addSql('CREATE INDEX IDX_7D3656A479F37AE5 ON account (id_user_id)');
        $this->addSql('COMMENT ON COLUMN account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account.id_exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, code_iso VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN currency.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE exchange (id UUID NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN exchange.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE holding (id INT NOT NULL, id_user_id UUID NOT NULL, id_currency_id UUID NOT NULL, id_exchange_id UUID NOT NULL, quantity NUMERIC(10, 2) NOT NULL, average_purchase_price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BBFD81679F37AE5 ON holding (id_user_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD81620364C81 ON holding (id_currency_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD81670BD1C57 ON holding (id_exchange_id)');
        $this->addSql('COMMENT ON COLUMN holding.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.id_currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.id_exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refresh_token (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F2195C74F2195 ON refresh_token (refresh_token)');
        $this->addSql('CREATE TABLE transaction (id UUID NOT NULL, id_exchange_id UUID NOT NULL, id_user_id UUID NOT NULL, id_currency_id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, value DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, order_direction VARCHAR(255) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D170BD1C57 ON transaction (id_exchange_id)');
        $this->addSql('CREATE INDEX IDX_723705D179F37AE5 ON transaction (id_user_id)');
        $this->addSql('CREATE INDEX IDX_723705D120364C81 ON transaction (id_currency_id)');
        $this->addSql('COMMENT ON COLUMN transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.id_exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.id_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.id_currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, birthdate DATE DEFAULT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, plain_password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A470BD1C57 FOREIGN KEY (id_exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A479F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81679F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81620364C81 FOREIGN KEY (id_currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81670BD1C57 FOREIGN KEY (id_exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D170BD1C57 FOREIGN KEY (id_exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D179F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D120364C81 FOREIGN KEY (id_currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE holding_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A470BD1C57');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A479F37AE5');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81679F37AE5');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81620364C81');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81670BD1C57');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D170BD1C57');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D179F37AE5');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D120364C81');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE holding');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
    }
}
