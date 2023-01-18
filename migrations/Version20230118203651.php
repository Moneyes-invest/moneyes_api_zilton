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
final class Version20230118203651 extends AbstractMigration
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
        $this->addSql('CREATE TABLE account (id UUID NOT NULL, exchange_id UUID NOT NULL, user_id UUID NOT NULL, private_key VARCHAR(255) DEFAULT NULL, public_key VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D3656A468AFD1A0 ON account (exchange_id)');
        $this->addSql('CREATE INDEX IDX_7D3656A4A76ED395 ON account (user_id)');
        $this->addSql('COMMENT ON COLUMN account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE asset (id UUID NOT NULL, asset_id UUID NOT NULL, symbol_id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2AF5A5C5DA1941 ON asset (asset_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5CC0F75674 ON asset (symbol_id)');
        $this->addSql('COMMENT ON COLUMN asset.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset.symbol_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE binance_account (id UUID NOT NULL, private_key VARCHAR(255) DEFAULT NULL, public_key VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN binance_account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, code_iso VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN currency.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE exchange (id UUID NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN exchange.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE holding (id INT NOT NULL, user_id UUID NOT NULL, currency_id UUID NOT NULL, exchange_id UUID NOT NULL, quantity DOUBLE PRECISION NOT NULL, average_purchase_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BBFD816A76ED395 ON holding (user_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD81638248176 ON holding (currency_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD81668AFD1A0 ON holding (exchange_id)');
        $this->addSql('COMMENT ON COLUMN holding.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refresh_token (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F2195C74F2195 ON refresh_token (refresh_token)');
        $this->addSql('CREATE TABLE transaction (id UUID NOT NULL, exchange_id UUID NOT NULL, user_id UUID NOT NULL, currency_id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, order_direction VARCHAR(255) NOT NULL, fees DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, quantity DOUBLE PRECISION NOT NULL, transaction_exchange_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D168AFD1A0 ON transaction (exchange_id)');
        $this->addSql('CREATE INDEX IDX_723705D1A76ED395 ON transaction (user_id)');
        $this->addSql('CREATE INDEX IDX_723705D138248176 ON transaction (currency_id)');
        $this->addSql('COMMENT ON COLUMN transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, birthdate DATE DEFAULT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, plain_password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A468AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C5DA1941 FOREIGN KEY (asset_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CC0F75674 FOREIGN KEY (symbol_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD816A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81638248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD81668AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D168AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D138248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE holding_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A468AFD1A0');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C5DA1941');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5CC0F75674');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD816A76ED395');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81638248176');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD81668AFD1A0');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D168AFD1A0');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D138248176');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE binance_account');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE holding');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
