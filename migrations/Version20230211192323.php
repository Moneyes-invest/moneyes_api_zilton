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
final class Version20230211192323 extends AbstractMigration
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
        $this->addSql('CREATE SEQUENCE transfert_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE account (id UUID NOT NULL, exchange_id UUID NOT NULL, user_id UUID NOT NULL, private_key VARCHAR(255) DEFAULT NULL, public_key VARCHAR(255) DEFAULT NULL, exchange VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D3656A468AFD1A0 ON account (exchange_id)');
        $this->addSql('CREATE INDEX IDX_7D3656A4A76ED395 ON account (user_id)');
        $this->addSql('COMMENT ON COLUMN account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE asset (id UUID NOT NULL, code VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2AF5A5C77153098 ON asset (code)');
        $this->addSql('COMMENT ON COLUMN asset.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE asset_exchange (asset_id UUID NOT NULL, exchange_id UUID NOT NULL, PRIMARY KEY(asset_id, exchange_id))');
        $this->addSql('CREATE INDEX IDX_5150AAF95DA1941 ON asset_exchange (asset_id)');
        $this->addSql('CREATE INDEX IDX_5150AAF968AFD1A0 ON asset_exchange (exchange_id)');
        $this->addSql('COMMENT ON COLUMN asset_exchange.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset_exchange.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE binance_account (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN binance_account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, code_iso VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN currency.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE exchange (id UUID NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN exchange.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE holding (id INT NOT NULL, account_id UUID NOT NULL, asset_id UUID NOT NULL, quantity DOUBLE PRECISION NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BBFD8169B6B5FBA ON holding (account_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD8165DA1941 ON holding (asset_id)');
        $this->addSql('COMMENT ON COLUMN holding.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refresh_token (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F2195C74F2195 ON refresh_token (refresh_token)');
        $this->addSql('CREATE TABLE symbol (id UUID NOT NULL, code VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ECC836F977153098 ON symbol (code)');
        $this->addSql('COMMENT ON COLUMN symbol.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE symbol_exchange (symbol_id UUID NOT NULL, exchange_id UUID NOT NULL, PRIMARY KEY(symbol_id, exchange_id))');
        $this->addSql('CREATE INDEX IDX_1A78D037C0F75674 ON symbol_exchange (symbol_id)');
        $this->addSql('CREATE INDEX IDX_1A78D03768AFD1A0 ON symbol_exchange (exchange_id)');
        $this->addSql('COMMENT ON COLUMN symbol_exchange.symbol_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN symbol_exchange.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE transaction (id UUID NOT NULL, account_id UUID NOT NULL, symbol_id UUID NOT NULL, asset_fees_id UUID DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, order_direction VARCHAR(255) NOT NULL, fees DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, quantity DOUBLE PRECISION NOT NULL, external_transaction_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D19B6B5FBA ON transaction (account_id)');
        $this->addSql('CREATE INDEX IDX_723705D1C0F75674 ON transaction (symbol_id)');
        $this->addSql('CREATE INDEX IDX_723705D1FF859273 ON transaction (asset_fees_id)');
        $this->addSql('COMMENT ON COLUMN transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.symbol_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.asset_fees_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE transfert (id INT NOT NULL, asset_id UUID NOT NULL, asset_fees_id UUID DEFAULT NULL, account_id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, quantity DOUBLE PRECISION NOT NULL, fees DOUBLE PRECISION DEFAULT NULL, external_transfert_id VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E4EACBB5DA1941 ON transfert (asset_id)');
        $this->addSql('CREATE INDEX IDX_1E4EACBBFF859273 ON transfert (asset_fees_id)');
        $this->addSql('CREATE INDEX IDX_1E4EACBB9B6B5FBA ON transfert (account_id)');
        $this->addSql('COMMENT ON COLUMN transfert.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transfert.asset_fees_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transfert.account_id IS \'(DC2Type:uuid)\'');
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
        $this->addSql('ALTER TABLE asset_exchange ADD CONSTRAINT FK_5150AAF95DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_exchange ADD CONSTRAINT FK_5150AAF968AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE binance_account ADD CONSTRAINT FK_1AD8DF63BF396750 FOREIGN KEY (id) REFERENCES account (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8169B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8165DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE symbol_exchange ADD CONSTRAINT FK_1A78D037C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE symbol_exchange ADD CONSTRAINT FK_1A78D03768AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1FF859273 FOREIGN KEY (asset_fees_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfert ADD CONSTRAINT FK_1E4EACBB5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfert ADD CONSTRAINT FK_1E4EACBBFF859273 FOREIGN KEY (asset_fees_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfert ADD CONSTRAINT FK_1E4EACBB9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE holding_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE transfert_id_seq CASCADE');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A468AFD1A0');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE asset_exchange DROP CONSTRAINT FK_5150AAF95DA1941');
        $this->addSql('ALTER TABLE asset_exchange DROP CONSTRAINT FK_5150AAF968AFD1A0');
        $this->addSql('ALTER TABLE binance_account DROP CONSTRAINT FK_1AD8DF63BF396750');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD8169B6B5FBA');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD8165DA1941');
        $this->addSql('ALTER TABLE symbol_exchange DROP CONSTRAINT FK_1A78D037C0F75674');
        $this->addSql('ALTER TABLE symbol_exchange DROP CONSTRAINT FK_1A78D03768AFD1A0');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D19B6B5FBA');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1C0F75674');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1FF859273');
        $this->addSql('ALTER TABLE transfert DROP CONSTRAINT FK_1E4EACBB5DA1941');
        $this->addSql('ALTER TABLE transfert DROP CONSTRAINT FK_1E4EACBBFF859273');
        $this->addSql('ALTER TABLE transfert DROP CONSTRAINT FK_1E4EACBB9B6B5FBA');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE asset_exchange');
        $this->addSql('DROP TABLE binance_account');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE holding');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE symbol');
        $this->addSql('DROP TABLE symbol_exchange');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transfert');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
